<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard - Banners') }}
        </h2>
    </x-slot>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-6" x-data="bannerManager()">
        @for ($i = 1; $i <= 10; $i++)
            <div class="p-4 border rounded-md shadow-sm">
                <label class="block font-semibold mb-2">Banner {{ $i }}</label>

                @if ($banners->has($i))
                    <!-- Existing banner: show preview, sizes, update + delete -->
                    <img src="{{ asset('storage/' . $banners[$i]->image) }}" class="mb-3 w-full h-40 object-cover rounded">
                    
                    <!-- Display current sizes -->
                    @if($banners[$i]->sizes && count($banners[$i]->sizes) > 0)
                        <div class="mb-3">
                            <span class="text-sm font-semibold text-gray-600">Current Sizes:</span>
                            <div class="flex flex-wrap gap-1 mt-1">
                                @foreach($banners[$i]->sizes as $size)
                                    <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">{{ $size }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Update Form -->
                    <form action="{{ route('banners.update', $banners[$i]->id) }}" method="POST" enctype="multipart/form-data" class="mb-2">
                        @csrf
                        @method('PUT')
                        
                        <!-- Image Update -->
                        <div class="mb-3">
                            <label class="text-sm text-gray-600 block mb-1">Update Image:</label>
                            <input type="file" name="image" class="block w-full text-sm border rounded px-2 py-1">
                        </div>
                        
                        <!-- Sizes Update -->
                        <div class="mb-3">
                            <label class="block text-sm font-medium mb-2">Sizes</label>
                            <div class="space-y-3">
                                <!-- Predefined Sizes -->
                                <div class="space-y-2">
                                    <label class="text-xs font-medium text-gray-700">Select sizes:</label>
                                    <div class="flex flex-wrap gap-2">
                                        @php
                                            $currentSizes = $banners[$i]->sizes ?? [];
                                        @endphp
                                        <label class="flex items-center">
                                            <input type="checkbox" name="sizes[]" value="Small" 
                                                   {{ in_array('Small', $currentSizes) ? 'checked' : '' }} 
                                                   class="mr-1">
                                            <span class="text-xs">Small</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="checkbox" name="sizes[]" value="Medium" 
                                                   {{ in_array('Medium', $currentSizes) ? 'checked' : '' }} 
                                                   class="mr-1">
                                            <span class="text-xs">Medium</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="checkbox" name="sizes[]" value="Large" 
                                                   {{ in_array('Large', $currentSizes) ? 'checked' : '' }} 
                                                   class="mr-1">
                                            <span class="text-xs">Large</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="checkbox" name="sizes[]" value="XL" 
                                                   {{ in_array('XL', $currentSizes) ? 'checked' : '' }} 
                                                   class="mr-1">
                                            <span class="text-xs">XL</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="checkbox" name="sizes[]" value="XXL" 
                                                   {{ in_array('XXL', $currentSizes) ? 'checked' : '' }} 
                                                   class="mr-1">
                                            <span class="text-xs">XXL</span>
                                        </label>
                                    </div>
                                </div>
                                
                                <!-- Custom Sizes -->
                                <div class="border-t pt-2">
                                    <label class="text-xs font-medium text-gray-700 mb-1 block">Custom sizes:</label>
                                    
                                    <!-- Display existing custom sizes -->
                                    @php
                                        $predefinedSizes = ['Small', 'Medium', 'Large', 'XL', 'XXL'];
                                        $customSizes = array_filter($currentSizes, function($size) use ($predefinedSizes) {
                                            return !in_array($size, $predefinedSizes);
                                        });
                                    @endphp
                                    
                                    @if(count($customSizes) > 0)
                                        <div class="mb-2 flex flex-wrap gap-1" id="existing-custom-{{ $i }}">
                                            @foreach($customSizes as $size)
                                                <div class="flex items-center bg-blue-100 px-2 py-1 rounded">
                                                    <span class="text-xs">{{ $size }}</span>
                                                    <input type="hidden" name="sizes[]" value="{{ $size }}">
                                                    <button type="button" onclick="removeCustomSize(this)" 
                                                            class="ml-1 text-red-500 hover:text-red-700 text-xs">×</button>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                    
                                    <!-- Add new custom size -->
                                    <div class="flex gap-1">
                                        <input type="text" id="custom-input-{{ $i }}" 
                                               placeholder="Enter custom size" 
                                               class="flex-1 border px-2 py-1 rounded text-xs">
                                        <button type="button" onclick="addCustomSizeUpdate({{ $i }})" 
                                                class="px-2 py-1 bg-green-500 text-white rounded hover:bg-green-600 text-xs">
                                            Add
                                        </button>
                                    </div>
                                    
                                    <!-- Container for new custom sizes -->
                                    <div id="new-custom-{{ $i }}" class="mt-1 flex flex-wrap gap-1"></div>
                                </div>
                            </div>
                        </div>
                        
                        <button class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600 w-full text-sm">Update</button>
                    </form>

                    <!-- Delete -->
                    <form action="{{ route('banners.destroy', $banners[$i]->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700 w-full text-sm">Delete</button>
                    </form>
                @else
                    <!-- New banner -->
                    <form action="{{ route('banners.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="position" value="{{ $i }}">
                        
                        <!-- Image -->
                        <div class="mb-3">
                            <label class="text-sm text-gray-600 block mb-1">Image:</label>
                            <input type="file" name="image" required class="block w-full text-sm border rounded px-2 py-1">
                        </div>
                        
                        <!-- Sizes -->
                        <div class="mb-3">
                            <label class="block text-sm font-medium mb-2">Sizes (optional)</label>
                            <div class="space-y-3">
                                <!-- Predefined Sizes -->
                                <div class="space-y-2">
                                    <label class="text-xs font-medium text-gray-700">Select sizes:</label>
                                    <div class="flex flex-wrap gap-2">
                                        <label class="flex items-center">
                                            <input type="checkbox" name="sizes[]" value="Small" class="mr-1">
                                            <span class="text-xs">Small</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="checkbox" name="sizes[]" value="Medium" class="mr-1">
                                            <span class="text-xs">Medium</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="checkbox" name="sizes[]" value="Large" class="mr-1">
                                            <span class="text-xs">Large</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="checkbox" name="sizes[]" value="XL" class="mr-1">
                                            <span class="text-xs">XL</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="checkbox" name="sizes[]" value="XXL" class="mr-1">
                                            <span class="text-xs">XXL</span>
                                        </label>
                                    </div>
                                </div>
                                
                                <!-- Custom Size -->
                                <div class="border-t pt-2">
                                    <label class="text-xs font-medium text-gray-700 mb-1 block">Custom size:</label>
                                    <div class="flex gap-1">
                                        <input type="text" id="new-custom-input-{{ $i }}" 
                                               placeholder="Enter custom size" 
                                               class="flex-1 border px-2 py-1 rounded text-xs">
                                        <button type="button" onclick="addCustomSizeNew({{ $i }})" 
                                                class="px-2 py-1 bg-green-500 text-white rounded hover:bg-green-600 text-xs">
                                            Add
                                        </button>
                                    </div>
                                    <!-- Container for custom sizes -->
                                    <div id="new-banner-custom-{{ $i }}" class="mt-1 flex flex-wrap gap-1"></div>
                                </div>
                            </div>
                        </div>
                        
                        <button class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 w-full text-sm">Upload</button>
                    </form>
                @endif
            </div>
        @endfor
    </div>

    <script>
        function bannerManager() {
            return {
                // You can add Alpine.js functionality here if needed
            }
        }

        function addCustomSizeUpdate(bannerId) {
            const input = document.getElementById(`custom-input-${bannerId}`);
            const container = document.getElementById(`new-custom-${bannerId}`);
            
            if (input.value.trim() && !isDuplicateSize(bannerId, input.value.trim())) {
                const sizeDiv = document.createElement('div');
                sizeDiv.className = 'flex items-center bg-blue-100 px-2 py-1 rounded';
                sizeDiv.innerHTML = `
                    <span class="text-xs">${input.value.trim()}</span>
                    <input type="hidden" name="sizes[]" value="${input.value.trim()}">
                    <button type="button" onclick="removeCustomSize(this)" 
                            class="ml-1 text-red-500 hover:text-red-700 text-xs">×</button>
                `;
                container.appendChild(sizeDiv);
                input.value = '';
            }
        }

        function addCustomSizeNew(bannerId) {
            const input = document.getElementById(`new-custom-input-${bannerId}`);
            const container = document.getElementById(`new-banner-custom-${bannerId}`);
            
            if (input.value.trim() && !isDuplicateNewSize(bannerId, input.value.trim())) {
                const sizeDiv = document.createElement('div');
                sizeDiv.className = 'flex items-center bg-blue-100 px-2 py-1 rounded';
                sizeDiv.innerHTML = `
                    <span class="text-xs">${input.value.trim()}</span>
                    <input type="hidden" name="sizes[]" value="${input.value.trim()}">
                    <button type="button" onclick="removeCustomSize(this)" 
                            class="ml-1 text-red-500 hover:text-red-700 text-xs">×</button>
                `;
                container.appendChild(sizeDiv);
                input.value = '';
            }
        }

        function removeCustomSize(button) {
            button.parentElement.remove();
        }

        function isDuplicateSize(bannerId, newSize) {
            const existingContainer = document.getElementById(`existing-custom-${bannerId}`);
            const newContainer = document.getElementById(`new-custom-${bannerId}`);
            
            let existingSizes = [];
            if (existingContainer) {
                existingSizes = Array.from(existingContainer.querySelectorAll('span')).map(span => span.textContent.trim());
            }
            if (newContainer) {
                existingSizes = existingSizes.concat(Array.from(newContainer.querySelectorAll('span')).map(span => span.textContent.trim()));
            }
            
            return existingSizes.includes(newSize);
        }

        function isDuplicateNewSize(bannerId, newSize) {
            const container = document.getElementById(`new-banner-custom-${bannerId}`);
            const existingSizes = Array.from(container.querySelectorAll('span')).map(span => span.textContent.trim());
            return existingSizes.includes(newSize);
        }
    </script>
</x-app-layout>