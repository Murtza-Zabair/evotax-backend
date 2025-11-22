<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Message;
use App\Notifications\NewMessageReceived;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class ContactController extends Controller
{
    public function index()
    {
        $contacts = Contact::latest()->get();
        return view('admin.contact', compact('contacts'));
    }

    public function messageIndex()
    {
        $messages = Message::latest()->paginate(15);
        return view('admin.message', compact('messages'));
    }
    
    public function submit(Request $request)
    {
        Log::info('Contact form submission:', $request->all());
       
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email',
                'contact' => 'required|string|max:20',
                'address' => 'nullable|string|max:1000',
                'message' => 'required|string',
                'orders' => 'required|array',
                'orders.*.id' => 'required|integer',
                'orders.*.name' => 'required|string', // Changed from title to name
                'orders.*.category' => 'required|string', // Changed to string
                'orders.*.image' => 'nullable|string',
                'orders.*.hoverImage' => 'nullable|string', // Added hoverImage
                'orders.*.quantity' => 'required|integer|min:1', // Changed from stock to quantity
            ]);

            // Clean and normalize the orders data
            $orders = collect($validated['orders'])->map(function ($order) {
                return [
                    'id' => (int) $order['id'],
                    'name' => trim($order['name']),
                    'category' => $order['category'],
                    'image' => $order['image'] ?? null,
                    'hoverImage' => $order['hoverImage'] ?? null,
                    'quantity' => (int) $order['quantity'],
                ];
            })->toArray();

            Log::info('Processed orders data:', $orders);

            $contact = Contact::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'contact' => $validated['contact'],
                'address' => $validated['address'] ?? null,
                'message' => $validated['message'],
                'orders' => json_encode($orders, JSON_UNESCAPED_SLASHES),
            ]);

            Log::info('Contact created successfully:', [
                'id' => $contact->id,
                'orders_stored' => $contact->orders
            ]);

            $this->sendContactEmail($contact, $orders);

            return response()->json([
                'success' => true,
                'message' => 'Order submitted successfully!',
                'data' => [
                    'id' => $contact->id,
                    'name' => $contact->name,
                    'email' => $contact->email,
                    'contact' => $contact->contact,
                    'address' => $contact->address,
                    'orders_count' => count($orders),
                    'total_items' => collect($orders)->sum('quantity')
                ]
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error:', $e->errors());
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Error creating contact:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
           
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit order',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    private function sendContactEmail($contact, $orders)
    {
        try {
            $ordersList = collect($orders)->map(function ($order) {
                return "- {$order['name']} x {$order['quantity']} ({$order['category']})";
            })->implode("\n");

            $totalItems = collect($orders)->sum('quantity');

            $emailBody = "New Order from {$contact->name}\n\n" .
                        "Contact Details:\n" .
                        "Email: {$contact->email}\n" .
                        "Phone: {$contact->contact}\n" .
                        "Address: {$contact->address}\n\n" .
                        "Message: {$contact->message}\n\n" .
                        "Order Summary:\n" .
                        "- Total Items: {$totalItems}\n" .
                        "- Order ID: {$contact->id}\n\n" .
                        "Products Ordered:\n" .
                        $ordersList;

            Mail::raw($emailBody, function ($mail) use ($contact) {
                $mail->to('murtzazabair@gmail.com')
                     ->subject("New Order #{$contact->id} from {$contact->name}");
            });

            Log::info('Email sent successfully for contact ID: ' . $contact->id);

        } catch (\Exception $e) {
            Log::error('Failed to send email:', ['error' => $e->getMessage()]);
        }
    }
    public function message(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone'   => 'nullable|string|max:20',
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $message = Message::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'message' => $request->message,
            ]);

            // Automatically queued because notification implements ShouldQueue
            $ownerEmail = env('OWNER_EMAIL');
            Notification::route('mail', $ownerEmail)
                ->notify(new NewMessageReceived($message));

            return response()->json([
                'status' => 'success',
                'message' => 'Your message has been saved.',
                'data' => $message,
            ]);
        } catch (\Exception $e) {
            Log::error('Error saving message:', ['error' => $e->getMessage()]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to save message',
            ], 500);
        }
    }
}