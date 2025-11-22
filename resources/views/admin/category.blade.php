<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard - Categories') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8" x-data="categoryManager()">

            {{-- Success/Error Messages --}}
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-100 border border-red-300 text-red-700 rounded">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Categories Section --}}
            <div>
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-2xl font-bold text-gray-700">All Categories</h3>
                    <button @click="openAddCategory = true"
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        + Add New Category
                    </button>
                </div>

                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Products Count</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($categories as $category)
                                <tr>
                                    <td class="px-6 py-4">{{ $category->id }}</td>
                                    <td class="px-6 py-4 font-medium">{{ $category->name }}</td>
                                    <td class="px-6 py-4">{{ $category->products->count() ?? 0 }}</td>
                                    <td class="px-6 py-4 space-x-2">
                                        <button @click="openEditCategoryModal(@json($category))" 
                                                class="text-yellow-600 hover:underline">Edit</button>
                                        <form action="{{ route('categories.destroy', $category->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button onclick="return confirm('Are you sure? All products in this category will be affected.')" 
                                                    class="text-red-600 hover:underline">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">No categories found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $categories->links() }}
                </div>
            </div>

            {{-- Add Category Modal --}}
            <div x-show="openAddCategory" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex items-center justify-center min-h-screen">
                    <div @click="openAddCategory = false" class="fixed inset-0 bg-black opacity-50"></div>
                    <div class="bg-white p-6 rounded-lg shadow-lg relative z-50 w-full max-w-md">
                        <h3 class="text-lg font-medium mb-4">Add New Category</h3>
                        <form action="{{ route('categories.store') }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label class="block text-sm font-medium mb-1">Category Name</label>
                                <input type="text" name="name" class="w-full border-gray-300 rounded-md" required>
                            </div>
                            <div class="flex justify-end space-x-2">
                                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Save</button>
                                <button type="button" @click="openAddCategory = false" class="bg-gray-300 px-4 py-2 rounded hover:bg-gray-400">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Edit Category Modal --}}
            <div x-show="openEditCategory" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex items-center justify-center min-h-screen">
                    <div @click="openEditCategory = false" class="fixed inset-0 bg-black opacity-50"></div>
                    <div class="bg-white p-6 rounded-lg shadow-lg relative z-50 w-full max-w-md">
                        <h3 class="text-lg font-medium mb-4">Edit Category</h3>
                        <form :action="`/categories/${editCategory.id}`" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-4">
                                <label class="block text-sm font-medium mb-1">Category Name</label>
                                <input type="text" name="name" x-model="editCategory.name" class="w-full border-gray-300 rounded-md" required>
                            </div>
                            <div class="flex justify-end space-x-2">
                                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Update</button>
                                <button type="button" @click="openEditCategory = false" class="bg-gray-300 px-4 py-2 rounded hover:bg-gray-400">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        function categoryManager() {
            return {
                openAddCategory: false,
                openEditCategory: false,
                editCategory: {},
                openEditCategoryModal(category) {
                    this.editCategory = { ...category };
                    this.openEditCategory = true;
                }
            }
        }
    </script>
</x-app-layout>