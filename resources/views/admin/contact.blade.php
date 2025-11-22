<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard - Contact Orders') }}
        </h2>
    </x-slot>

    <!-- Add AlpineJS CDN if not already included in your layout -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <div class="py-10 px-6 max-w-7xl mx-auto" x-data="{
        showModal: false,
        currentOrder: null,
        currentContact: null,
        openModal(orderData, contactInfo) {
            try {
                console.log('Raw order data received:', orderData);
                console.log('Contact info received:', contactInfo);
                console.log('Type of orderData:', typeof orderData);
                console.log('Is array:', Array.isArray(orderData));
                
                this.currentOrder = Array.isArray(orderData) ? orderData : [orderData];
                this.currentContact = contactInfo;
                console.log('Final currentOrder:', this.currentOrder);
                console.log('Final currentContact:', this.currentContact);
                this.showModal = true;
            } catch (e) {
                console.error('Error opening modal:', e);
                alert('Error displaying order details: ' + e.message);
            }
        }
    }">
        <h2 class="text-2xl font-bold mb-6 text-gray-800">All Contact Orders</h2>

        @if ($contacts->isEmpty())
            <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative">
                No contact orders yet.
            </div>
        @else
            <div class="overflow-x-auto bg-white shadow-md rounded-lg">
                <table class="min-w-full text-sm text-left text-gray-700">
                    <thead class="text-xs text-white uppercase bg-gray-700">
                        <tr>
                            <th class="px-6 py-3">#</th>
                            <th class="px-6 py-3">Name</th>
                            <th class="px-6 py-3">Email</th>
                            <th class="px-6 py-3">Contact</th>
                            <th class="px-6 py-3">Address</th>
                            <th class="px-6 py-3">Message</th>
                            <th class="px-6 py-3">Order Details</th>
                            <th class="px-6 py-3">Date</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($contacts as $contact)
                            <tr>
                                <td class="px-6 py-4">{{ $loop->iteration }}</td>
                                <td class="px-6 py-4 font-medium">{{ $contact->name }}</td>
                                <td class="px-6 py-4">{{ $contact->email }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                        </svg>
                                        <span class="font-medium text-gray-900">{{ $contact->contact }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($contact->address)
                                        <div class="max-w-xs">
                                            <p class="text-xs text-gray-600 line-clamp-2" title="{{ $contact->address }}">
                                                {{ Str::limit($contact->address, 50) }}
                                            </p>
                                        </div>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="max-w-xs">
                                        <p class="text-xs text-gray-600 line-clamp-2" title="{{ $contact->message }}">
                                            {{ Str::limit($contact->message, 50) }}
                                        </p>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        // Handle JSON string from database
                                        $orders = [];
                                        
                                        if (is_string($contact->orders)) {
                                            $orders = json_decode($contact->orders, true) ?? [];
                                        } elseif (is_array($contact->orders)) {
                                            $orders = $contact->orders;
                                        }

                                        // Prepare contact info for modal
                                        $contactInfo = [
                                            'name' => $contact->name,
                                            'email' => $contact->email,
                                            'contact' => $contact->contact,
                                            'address' => $contact->address,
                                            'message' => $contact->message,
                                            'created_at' => $contact->created_at->format('d M Y, h:i A')
                                        ];
                                    @endphp

                                    @if (!empty($orders))
                                        <button @click="openModal(@js($orders), @js($contactInfo))" 
                                                class="inline-flex items-center px-3 py-1 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md transition-colors duration-200">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            View Orders ({{ count($orders) }})
                                        </button>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 text-xs font-medium text-red-700 bg-red-100 rounded-full">
                                            No data
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $contact->created_at->format('d M Y, h:i A') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <!-- Order Details Modal -->
        <div x-show="showModal" 
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto"
             x-transition>
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-500 opacity-75" @click="showModal = false"></div>
                </div>

                <!-- Modal panel -->
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-6xl sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                                    Order Details
                                </h3>

                                <!-- Customer Information Section -->
                                <template x-if="currentContact">
                                    <div class="bg-gray-50 rounded-lg p-4 mb-6">
                                        <h4 class="text-lg font-semibold text-gray-900 mb-3">Customer Information</h4>
                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 text-sm">
                                            <div class="bg-white p-3 rounded border">
                                                <span class="font-medium text-gray-700">Name:</span><br>
                                                <span class="text-gray-900" x-text="currentContact.name"></span>
                                            </div>
                                            <div class="bg-white p-3 rounded border">
                                                <span class="font-medium text-gray-700">Email:</span><br>
                                                <span class="text-blue-600" x-text="currentContact.email"></span>
                                            </div>
                                            <div class="bg-white p-3 rounded border">
                                                <span class="font-medium text-gray-700">Contact:</span><br>
                                                <span class="text-green-600 font-semibold" x-text="currentContact.contact"></span>
                                            </div>
                                            <div class="bg-white p-3 rounded border md:col-span-2">
                                                <span class="font-medium text-gray-700">Address:</span><br>
                                                <span class="text-gray-900" x-text="currentContact.address || 'Not provided'"></span>
                                            </div>
                                            <div class="bg-white p-3 rounded border">
                                                <span class="font-medium text-gray-700">Order Date:</span><br>
                                                <span class="text-gray-900" x-text="currentContact.created_at"></span>
                                            </div>
                                        </div>
                                        <div class="mt-4 bg-white p-3 rounded border">
                                            <span class="font-medium text-gray-700">Message:</span><br>
                                            <p class="text-gray-900 mt-1" x-text="currentContact.message"></p>
                                        </div>
                                    </div>
                                </template>

                                <!-- Order Items Section -->
                                <div class="mt-2 space-y-4 max-h-[70vh] overflow-y-auto pr-2">
                                    <template x-if="currentOrder && currentOrder.length > 0">
                                        <div>
                                            <h4 class="text-lg font-semibold text-gray-900 mb-3">Ordered Items</h4>
                                            <template x-for="(order, index) in currentOrder" :key="index">
                                                <div class="border border-gray-200 rounded-lg p-4 mb-4 bg-gray-50">
                                                    <div class="flex items-start gap-4">
                                                        <!-- Product Image -->
                                                        <div class="flex-shrink-0">
                                                            <template x-if="order.image">
                                                                <img :src="`/storage/${order.image}`" 
                                                                     :alt="order.title || 'Product image'" 
                                                                     class="w-24 h-24 object-cover rounded-lg border border-gray-300"
                                                                     onerror="this.src='/images/no-image.png'; this.onerror=null;">
                                                            </template>
                                                            <template x-if="!order.image">
                                                                <div class="w-24 h-24 bg-gray-200 rounded-lg flex items-center justify-center text-gray-400 text-xs">
                                                                    No Image
                                                                </div>
                                                            </template>
                                                        </div>
                                                        
                                                        <div class="flex-1 min-w-0">
                                                            <!-- Product Title -->
                                                            <h4 class="text-lg font-semibold text-gray-900 mb-1" x-text="order.title || 'Untitled Product'"></h4>
                                                            
                                                            <!-- Category -->
                                                            <p class="text-sm text-gray-600 mb-3" x-text="order.category && order.category.name ? order.category.name : 'No Category'"></p>
                                                            
                                                            <!-- Size Badge -->
                                                            <div class="mb-3">
                                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4h4a2 2 0 002-2V5z"></path>
                                                                    </svg>
                                                                    Size: <span x-text="order.selectedSize || 'One Size'"></span>
                                                                </span>
                                                            </div>
                                                            
                                                            <!-- Product Details Grid -->
                                                            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 text-sm">
                                                                <div class="bg-white p-2 rounded">
                                                                    <span class="font-medium text-gray-700">Price:</span><br>
                                                                    <span class="text-green-600 font-semibold">$<span x-text="order.price ? parseFloat(order.price).toFixed(2) : '0.00'"></span></span>
                                                                </div>
                                                                <div class="bg-white p-2 rounded">
                                                                    <span class="font-medium text-gray-700">Stock:</span><br>
                                                                    <span class="text-blue-600 font-semibold" x-text="order.stock || 'N/A'"></span>
                                                                </div>
                                                                <div class="bg-white p-2 rounded">
                                                                    <span class="font-medium text-gray-700">Product ID:</span><br>
                                                                    <span class="text-gray-800 font-semibold" x-text="order.id || 'N/A'"></span>
                                                                </div>
                                                                <div class="bg-white p-2 rounded">
                                                                    <span class="font-medium text-gray-700">Category ID:</span><br>
                                                                    <span class="text-gray-800 font-semibold" x-text="order.category_id || 'N/A'"></span>
                                                                </div>
                                                            </div>
                                                            
                                                            <!-- Category Details (if available) -->
                                                            <template x-if="order.category && order.category.created_at">
                                                                <div class="mt-3 p-3 bg-white rounded-lg border">
                                                                    <h5 class="text-sm font-semibold text-gray-700 mb-2">Category Information:</h5>
                                                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2 text-xs text-gray-600">
                                                                        <div>
                                                                            <span class="font-medium">Name:</span> 
                                                                            <span x-text="order.category.name"></span>
                                                                        </div>
                                                                        <div>
                                                                            <span class="font-medium">Created:</span> 
                                                                            <span x-text="new Date(order.category.created_at).toLocaleDateString()"></span>
                                                                        </div>
                                                                        <div>
                                                                            <span class="font-medium">Updated:</span> 
                                                                            <span x-text="new Date(order.category.updated_at).toLocaleDateString()"></span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>

                                            <!-- Order Summary -->
                                            <div class="mt-6 bg-gray-100 rounded-lg p-4">
                                                <h4 class="text-lg font-semibold text-gray-900 mb-3">Order Summary</h4>
                                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                                    <div class="bg-white p-3 rounded">
                                                        <span class="font-medium text-gray-700">Total Items:</span><br>
                                                        <span class="text-lg font-bold text-blue-600" x-text="currentOrder.length"></span>
                                                    </div>
                                                    <div class="bg-white p-3 rounded">
                                                        <span class="font-medium text-gray-700">Total Value:</span><br>
                                                        <span class="text-lg font-bold text-green-600">
                                                            $<span x-text="currentOrder.reduce((total, order) => total + parseFloat(order.price || 0), 0).toFixed(2)"></span>
                                                        </span>
                                                    </div>
                                                    <div class="bg-white p-3 rounded">
                                                        <span class="font-medium text-gray-700">Unique Sizes:</span><br>
                                                        <span class="text-sm text-gray-600" x-text="[...new Set(currentOrder.map(order => order.selectedSize || 'One Size'))].join(', ')"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                    <template x-if="!currentOrder || currentOrder.length === 0">
                                        <div class="text-center py-8">
                                            <div class="text-red-500 text-lg font-medium">No order data available</div>
                                            <div class="text-gray-400 text-sm mt-2">The order information could not be loaded.</div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" 
                                @click="showModal = false"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</x-app-layout>