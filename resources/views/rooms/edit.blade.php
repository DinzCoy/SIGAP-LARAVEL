<x-app-layout>
    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6 flex items-center gap-4">
                <a href="{{ route('rooms.index') }}" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full transition-colors">
                    <x-lucide-arrow-left class="w-5 h-5" />
                </a>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Edit Ruangan</h2>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <form action="{{ route('rooms.update', $room->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Ruangan <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" required class="block w-full border-gray-300 rounded-md shadow-sm focus-ring-bps" value="{{ old('name', $room->name) }}">
                        @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label for="pic_id" class="block text-sm font-medium text-gray-700 mb-1">Penanggung Jawab (PIC)</label>
                        <select name="pic_id" id="pic_id" class="block w-full border-gray-300 rounded-md shadow-sm focus-ring-bps">
                            <option value="">-- Tidak ada PIC --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('pic_id', $room->pic_id) == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('pic_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-6">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                        <textarea name="description" id="description" rows="3" class="block w-full border-gray-300 rounded-md shadow-sm focus-ring-bps">{{ old('description', $room->description) }}</textarea>
                        @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('rooms.index') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Batal
                        </a>
                        <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-bps-blue hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Perbarui Ruangan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
