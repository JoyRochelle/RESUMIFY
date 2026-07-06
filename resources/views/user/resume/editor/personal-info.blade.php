<!-- Personal Info -->
                    <x-user.editor-accordion title="Personal Info" icon="person">
                        <form class="section-form" data-section-id="{{ $personal->id ?? '' }}">
                            <div class="grid grid-cols-1 gap-4">
                                <x-ui.form-input label="Full Name" name="name" value="{{ $personalContent['name'] ?? '' }}" class="auto-save" :required="true" placeholder="e.g. John Doe" />
                                <x-ui.form-input label="Professional Title" name="title" value="{{ $personalContent['title'] ?? '' }}" class="auto-save" :required="true" placeholder="e.g. Senior Product Designer" />
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <x-ui.form-input label="Email" name="email" type="email" value="{{ $personalContent['email'] ?? '' }}" class="auto-save" :required="true" placeholder="you@example.com" />
                                    <div class="relative group/input" x-data="{ focused: false, error: '' }">
                                        <label class="text-[11px] font-bold uppercase tracking-wider transition-colors duration-200 mb-1 flex items-center gap-1"
                                               :class="error ? 'text-red-500' : (focused ? 'text-secondary' : 'text-primary/60')">
                                            Phone Number
                                        </label>
                                        <div class="relative flex items-center border-b-2 border-primary/15 focus-within:border-secondary transition-all duration-200">
                                            <select name="country_code" class="auto-save bg-transparent py-2 pl-0 pr-6 outline-none text-primary text-sm appearance-none cursor-pointer border-none focus:ring-0 font-medium">
                                                @php
                                                    $codes = ['+1' => 'US (+1)', '+44' => 'UK (+44)', '+61' => 'AU (+61)', '+62' => 'ID (+62)', '+91' => 'IN (+91)'];
                                                    $selectedCode = $personalContent['country_code'] ?? '+62';
                                                @endphp
                                                @foreach($codes as $val => $label)
                                                    <option value="{{ $val }}" {{ $selectedCode === $val ? 'selected' : '' }}>{{ $label }}</option>
                                                @endforeach
                                            </select>
                                            <span class="material-symbols-outlined text-[16px] text-primary/40 pointer-events-none absolute left-[4.5rem]">arrow_drop_down</span>
                                            <div class="w-px h-4 bg-primary/20 mx-2"></div>
                                            <input
                                                name="phone"
                                                type="tel"
                                                value="{{ $personalContent['phone'] ?? '' }}"
                                                placeholder="812 xxxx xxxx"
                                                class="auto-save w-full bg-transparent py-2 px-0 outline-none focus:ring-0 text-primary text-sm placeholder:text-primary/30 border-none"
                                                @focus="focused = true; error = ''"
                                                @blur="focused = false; validate($event, 'tel', false)"
                                                x-on:input="if(error) validate($event, 'tel', false)"
                                            />
                                            <span class="absolute right-0 top-2 text-red-400 text-[16px] material-symbols-outlined transition-all duration-200"
                                                  x-show="error" style="display:none">error</span>
                                        </div>
                                        <p class="text-[11px] text-red-400 mt-1 leading-tight" x-show="error" x-text="error" style="display:none"></p>
                                    </div>
                                </div>
                                <x-ui.form-input label="Location" name="location" value="{{ $personalContent['location'] ?? '' }}" class="auto-save" />
                                <div class="relative group mt-2">
                                    <label class="text-[11px] font-bold uppercase tracking-wider text-primary/60 mb-2 block">Professional Summary</label>
                                    <textarea name="summary" placeholder="Write 2–4 sentences about your background, key skills, and career goals..." class="auto-save w-full bg-surface-container-low rounded-lg border border-primary/10 focus:border-secondary focus:ring-0 p-4 text-sm text-primary leading-relaxed custom-scrollbar outline-none transition-colors duration-200 resize-none placeholder:text-primary/30" rows="5">{{ $personalContent['summary'] ?? '' }}</textarea>
                                    <button type="button" onclick="openRefineModal(this)" class="absolute bottom-3 right-3 text-[10px] font-bold bg-secondary/10 text-secondary hover:bg-secondary hover:text-white px-2 py-1 rounded transition-colors flex items-center gap-1 shadow-sm"><span class="material-symbols-outlined text-[12px]">auto_awesome</span>Refine</button>
                                </div>

                                {{-- Photo Upload: only shown when template supports show_photo --}}
                                @if($cv && !empty($cv->template->style_config['show_photo']))
                                <div id="photo-upload-section" class="mt-2">
                                    <label class="text-[11px] font-bold uppercase tracking-wider text-primary/60 mb-2 block flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[14px]">photo_camera</span>
                                        Profile Photo
                                    </label>
                                    <div class="flex items-center gap-4">
                                        <div id="photo-preview-wrap" class="w-20 h-20 rounded-full overflow-hidden border-2 border-primary/15 bg-surface-container-low flex items-center justify-center shrink-0">
                                            @if(!empty($personalContent['photo']))
                                                <img id="photo-preview-img" src="{{ $personalContent['photo'] }}" class="w-full h-full object-cover" alt="Profile">
                                            @else
                                                <span id="photo-placeholder-icon" class="material-symbols-outlined text-[32px] text-primary/20">person</span>
                                                <img id="photo-preview-img" src="" class="w-full h-full object-cover hidden" alt="Profile">
                                            @endif
                                        </div>
                                        <div class="flex-1">
                                            <label for="photo-file-input" class="block w-full cursor-pointer text-center py-2.5 px-3 rounded-xl border border-dashed border-secondary/40 text-secondary hover:bg-secondary/5 text-xs font-bold transition-colors">
                                                <span class="material-symbols-outlined text-[14px] align-middle mr-1">upload</span>
                                                {{ !empty($personalContent['photo']) ? 'Change Photo' : 'Upload Photo' }}
                                            </label>
                                            <input id="photo-file-input" type="file" accept="image/jpeg,image/png,image/webp" class="hidden" onchange="handlePhotoUpload(this)">
                                            <input type="hidden" name="photo" id="photo-hidden-input" value="{{ $personalContent['photo'] ?? '' }}">
                                            @if(!empty($personalContent['photo']))
                                            <button type="button" onclick="removePhoto()" class="mt-2 w-full text-[11px] text-red-400 hover:text-red-500 transition-colors text-center">Remove photo</button>
                                            @else
                                            <p class="text-[11px] text-primary/40 mt-1.5 text-center leading-tight">JPG, PNG, WebP · Max 2MB</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </form>
                    </x-user.editor-accordion>