<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard - All Products') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8" x-data="productManager()">

            {{-- Top Bar --}}
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-gray-700">All Products</h3>
                <a href="#" @click.prevent="openAdd = true"
                    class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                    + Add New Product
                </a>
            </div>

            {{-- Success Message --}}
            @if (session('success'))
                <div class="mb-4 text-green-700 bg-green-100 border border-green-300 rounded p-3">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Products Table --}}
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Image</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Hover Image</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Category</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Description</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($products as $product)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $product->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" alt="Image"
                                            class="w-16 h-16 object-cover rounded">
                                    @else
                                        <span class="text-gray-500">No Image</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($product->hover_image)
                                        <img src="{{ asset('storage/' . $product->hover_image) }}" alt="Hover Image"
                                            class="w-16 h-16 object-cover rounded">
                                    @else
                                        <span class="text-gray-500">No Hover Image</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $product->title }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $product->category->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700 max-w-xs">
                                    <div class="truncate">{{ $product->description ?? 'No description' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 space-x-2">
                                    <a href="#" @click.prevent='openEditModal(@json($product))'
                                        class="inline-block px-3 py-1 bg-yellow-400 text-white rounded hover:bg-yellow-500 transition">
                                        Edit
                                    </a>
                                    <form action="{{ route('products.destroy', $product->id) }}" method="POST"
                                        class="inline-block"
                                        onsubmit="return confirm('Are you sure you want to delete this product?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 transition">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">No products found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-6">
                {{ $products->links() }}
            </div>

            <!-- Add Product Modal -->
            <div x-show="openAdd" x-cloak
                class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                <div @click.away="openAdd = false" class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6 max-h-screen overflow-y-auto">
                    <h2 class="text-lg font-bold mb-4">Add New Product</h2>
                    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-1">Title</label>
                            <input name="title" class="w-full border px-3 py-2 rounded" required>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-1">Category</label>
                            <select name="category_id" class="w-full border px-3 py-2 rounded" required>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-1">Description</label>
                            <textarea name="description" rows="4" 
                                      class="w-full border px-3 py-2 rounded" 
                                      placeholder="Enter product description..."></textarea>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-1">Main Image</label>
                            <input type="file" name="image" class="w-full" accept="image/*">
                            <p class="text-xs text-gray-500 mt-1">This will be the default product image</p>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-1">Hover Image (Optional)</label>
                            <input type="file" name="hover_image" class="w-full" accept="image/*">
                            <p class="text-xs text-gray-500 mt-1">This image shows when user hovers over the product</p>
                        </div>
                        <div class="flex justify-end space-x-2">
                            <button type="button" @click="openAdd = false"
                                class="px-4 py-2 bg-gray-300 rounded">Cancel</button>
                            <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Save</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Edit Product Modal -->
            <div x-show="openEdit" x-cloak
                class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                <div @click.away="openEdit = false" class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6 max-h-screen overflow-y-auto">
                    <h2 class="text-lg font-bold mb-4">Edit Product</h2>
                    <form :action="'/products/' + editProduct.id" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-1">Title</label>
                            <input name="title" x-model="editProduct.title" class="w-full border px-3 py-2 rounded" required>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-1">Category</label>
                            <select name="category_id" class="w-full border px-3 py-2 rounded" required>
                                <template x-for="category in categories" :key="category.id">
                                    <option :value="category.id" x-text="category.name"
                                        :selected="category.id == editProduct.category_id"></option>
                                </template>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-1">Description</label>
                            <textarea name="description" rows="4" 
                                      x-model="editProduct.description"
                                      class="w-full border px-3 py-2 rounded" 
                                      placeholder="Enter product description..."></textarea>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-1">Main Image</label>
                            <input type="file" name="image" class="w-full" accept="image/*">
                            <template x-if="editProduct.image">
                                <div class="mt-2">
                                    <span class="text-sm text-gray-500">Current Image:</span>
                                    <img :src="'/storage/' + editProduct.image" class="w-24 h-24 object-cover rounded mt-1">
                                </div>
                            </template>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-1">Hover Image (Optional)</label>
                            <input type="file" name="hover_image" class="w-full" accept="image/*">
                            <template x-if="editProduct.hover_image">
                                <div class="mt-2">
                                    <span class="text-sm text-gray-500">Current Hover Image:</span>
                                    <img :src="'/storage/' + editProduct.hover_image" class="w-24 h-24 object-cover rounded mt-1">
                                </div>
                            </template>
                            <p class="text-xs text-gray-500 mt-1">This image shows when user hovers over the product</p>
                        </div>
                        <div class="flex justify-end space-x-2">
                            <button type="button" @click="openEdit = false"
                                class="px-4 py-2 bg-gray-300 rounded">Cancel</button>
                            <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function productManager() {
            return {
                openAdd: false,
                openEdit: false,
                editProduct: {},
                categories: @json($categories ?? []),

                openEditModal(product) {
                    this.editProduct = {
                        ...product,
                        category_id: product.category_id || '',
                        description: product.description || ''
                    };
                    this.openEdit = true;
                }
            }
        }
    </script>

</x-app-layout>