@props(['ocupaciones' => []])

<div
    x-data="{
        show: false,
        step: 'upload',
        file: null,
        fileName: '',
        fileSize: '',
        filePreview: '',
        prescription: null,
        errorMessage: '',
        dragActive: false,
        csrf: '{{ csrf_token() }}',
        showContactLens: false,
        ocupaciones: {{ Js::from($ocupaciones) }},
        idOcupacion: '',
        fechaNacimiento: '',
        tomSelect: null,
        tomSelectListo: false,
        validationErrors: [],
        showValidation: false,

        get canAnalyze() {
            return this.idOcupacion && this.fechaNacimiento && this.file;
        },

        init() {
            window.addEventListener('open-prescription', () => {
                this.show = true;
                this.resetState();
                if (!this.tomSelectListo) {
                    this.$nextTick(() => this.initTomSelect());
                }
            });
        },

        initTomSelect() {
            const select = this.$refs.ocupacionSelect;
            if (!select || select.tomselect) return;

            const self = this;
            try {
                self.tomSelect = new TomSelect(select, {
                    create: false,
                    sortField: [{ field: 'text', direction: 'asc' }],
                    placeholder: 'Seleccione su ocupación',
                    maxItems: 1,
                    maxOptions: null,
                    searchField: ['text'],
                    wrapperClass: 'ocupacion-select',
                    options: self.ocupaciones.map(o => ({
                        value: o.id,
                        text: o.nombre,
                    })),
                    onInitialize() {
                        self.idOcupacion = '';
                        self.tomSelectListo = true;
                    },
                    onChange(value) {
                        self.idOcupacion = value;
                    },
                });
            } catch (e) {
                console.error('TomSelect init error:', e);
            }
        },

        resetState() {
            this.step = 'upload';
            this.file = null;
            this.fileName = '';
            this.fileSize = '';
            this.filePreview = '';
            this.prescription = null;
            this.errorMessage = '';
            this.dragActive = false;
            this.idOcupacion = '';
            this.fechaNacimiento = '';
            this.validationErrors = [];
            this.showValidation = false;
            if (this.tomSelect) {
                this.tomSelect.clear();
            }
        },

        handleFileSelect(event) {
            const files = event.target.files;
            if (files.length) {
                this.processFile(files[0]);
            }
        },

        handleDrop(event) {
            const files = event.dataTransfer.files;
            if (files.length) {
                this.processFile(files[0]);
            }
            this.dragActive = false;
        },

        handleDragOver(event) {
            event.preventDefault();
            this.dragActive = true;
        },

        handleDragLeave() {
            this.dragActive = false;
        },

        processFile(file) {
            const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'application/pdf'];
            const maxSize = 10 * 1024 * 1024;

            if (!validTypes.includes(file.type)) {
                this.errorMessage = 'Formato no soportado. Sube una imagen (JPG, PNG, WebP) o PDF.';
                return;
            }

            if (file.size > maxSize) {
                this.errorMessage = 'El archivo no debe superar los 10 MB.';
                return;
            }

            this.file = file;
            this.fileName = file.name;
            this.fileSize = (file.size / 1024).toFixed(1) + ' KB';
            if (file.size > 1024 * 1024) {
                this.fileSize = (file.size / (1024 * 1024)).toFixed(1) + ' MB';
            }
            this.errorMessage = '';

            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.filePreview = e.target.result;
                };
                reader.readAsDataURL(file);
            } else {
                this.filePreview = '';
            }
        },

        removeFile() {
            this.file = null;
            this.fileName = '';
            this.fileSize = '';
            this.filePreview = '';
            this.fileInput && (this.fileInput.value = '');
            this.step = 'upload';
        },

        async startAnalysis() {
            const errors = [];
            if (!this.idOcupacion) errors.push('Selecciona tu ocupación para continuar.');
            if (!this.fechaNacimiento) errors.push('Ingresa tu fecha de nacimiento para continuar.');
            if (!this.file) errors.push('Sube tu receta óptica para continuar.');

            if (errors.length > 0) {
                this.validationErrors = errors;
                this.showValidation = true;
                return;
            }

            this.validationErrors = [];
            this.showValidation = false;
            this.step = 'loading';
            this.errorMessage = '';

            const formData = new FormData();
            formData.append('file', this.file);
            formData.append('id_ocupacion', this.idOcupacion);
            formData.append('fecha_nacimiento', this.fechaNacimiento);

            try {
                const response = await fetch('{{ route('prescription.analyze') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.csrf,
                    },
                });

                const result = await response.json();

                if (result.success) {
                    this.prescription = result.data;
                    this.step = 'result';
                } else {
                    throw new Error(result.error || 'Error al analizar la receta.');
                }
            } catch (err) {
                this.errorMessage = err.message || 'Ocurrió un error inesperado. Inténtalo de nuevo.';
                this.step = 'error';
            }
        },

        tryAgain() {
            this.resetState();
        },

        getFieldValue(path, def) {
            const keys = path.split('.');
            let value = this.prescription;
            for (const key of keys) {
                if (value && typeof value === 'object' && key in value) {
                    value = value[key];
                } else {
                    return def !== undefined ? def : '';
                }
            }
            return value !== null && value !== undefined ? value : (def !== undefined ? def : '');
        },

        setFieldValue(path, val) {
            const keys = path.split('.');
            let obj = this.prescription;
            for (let i = 0; i < keys.length - 1; i++) {
                if (!obj[keys[i]]) obj[keys[i]] = {};
                obj = obj[keys[i]];
            }
            obj[keys[keys.length - 1]] = val === '' ? null : val;
        },

        getComplejidadLabel(level) {
            const labels = {
                'baja': 'Baja',
                'estandar': 'Estándar',
                'media': 'Media',
                'alta': 'Alta',
            };
            return labels[level] || level || 'Estándar';
        },

        getComplejidadColor(level) {
            const colors = {
                'baja': 'bg-green-100 text-green-800',
                'estandar': 'bg-blue-100 text-blue-800',
                'media': 'bg-yellow-100 text-yellow-800',
                'alta': 'bg-red-100 text-red-800',
            };
            return colors[level] || colors['estandar'];
        },
    }"
     x-show="show"
     x-transition.opacity
     x-cloak
     class="fixed inset-0 z-[60] flex justify-center bg-black/70 backdrop-blur-sm p-4 overflow-y-auto"
 >
 
     {{-- Modal Container --}}
     <div
         @click.away="if (step !== 'loading') show = false"
         class="relative bg-white w-full max-w-4xl rounded-3xl shadow-2xl flex flex-col max-h-[92vh] my-auto"
     >
 
         {{-- Header --}}
         <div class="shrink-0 z-10 bg-gradient-to-r from-red-600 to-red-500 text-white px-6 md:px-8 py-5 flex items-center justify-between rounded-t-3xl">
             <div>
                 <h2 class="text-xl md:text-2xl font-bold">Asesor de Receta</h2>
                 <p class="text-white/80 text-sm mt-1" x-show="step === 'upload'">Sube tu receta óptica para análisis</p>
                 <p class="text-white/80 text-sm mt-1" x-show="step === 'loading'">Analizando tu receta...</p>
                 <p class="text-white/80 text-sm mt-1" x-show="step === 'result'">Valida y ajusta los valores detectados</p>
                 <p class="text-white/80 text-sm mt-1" x-show="step === 'error'">Algo salió mal</p>
             </div>
 
             <button
                 @click="show = false; resetState()"
                 x-show="step !== 'loading'"
                 class="text-white/80 hover:text-white transition-colors p-2 rounded-full hover:bg-white/10"
             >
                 <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                 </svg>
             </button>
         </div>
 
         {{-- Non-scrollable steps (upload, loading, error) --}}
         <div x-show="step !== 'result'" class="shrink-0">
 
          {{-- Upload Step --}}
          <div x-show="step === 'upload'" class="p-6 md:p-8">

            {{-- Occupation & Date of Birth --}}
            <div class="mb-6 grid md:grid-cols-2 gap-4">
                <div>
                    <label class="flex items-center gap-1.5 text-sm font-medium text-gray-700 mb-1.5">
                        Ocupación <span class="text-red-500">*</span>
                        <template x-if="idOcupacion">
                            <svg class="w-4 h-4 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" stroke-width="2"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 13l3 3 7-7"/>
                            </svg>
                        </template>
                    </label>
                    <select
                        x-ref="ocupacionSelect"
                        class="ocupacion-select w-full"
                    >
                        <option value=""></option>
                    </select>
                </div>

                <div>
                    <label class="flex items-center gap-1.5 text-sm font-medium text-gray-700 mb-1.5">
                        Fecha de nacimiento
                        <template x-if="fechaNacimiento">
                            <svg class="w-4 h-4 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" stroke-width="2"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 13l3 3 7-7"/>
                            </svg>
                        </template>
                    </label>
                    <input
                        type="date"
                        x-model="fechaNacimiento"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-colors"
                    >
                </div>
            </div>

            {{-- File Upload Zone --}}
            <template x-if="!file">
            <div
                @dragover.prevent="handleDragOver"
                @dragleave="handleDragLeave"
                @drop.prevent="handleDrop"
                @click="$refs.fileInput.click()"
                :class="dragActive ? 'border-red-500 bg-red-50' : 'border-gray-300 bg-gray-50 hover:border-red-400 hover:bg-red-50/50'"
                class="border-2 border-dashed rounded-2xl p-10 md:p-14 text-center cursor-pointer transition-all duration-200"
            >
                <div class="flex flex-col items-center gap-4">
                    <div class="w-20 h-20 rounded-full bg-red-100 flex items-center justify-center">
                        <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                    </div>

                    <div>
                        <p class="text-lg font-semibold text-gray-800">
                            Arrastra tu receta aquí
                        </p>
                        <p class="text-sm text-gray-500 mt-1">
                            o haz clic para seleccionar
                        </p>
                    </div>

                    <p class="text-xs text-gray-400">
                        Formatos: JPG, PNG, WebP, PDF &middot; Máximo 10 MB
                    </p>
                </div>

                <input
                    type="file"
                    x-ref="fileInput"
                    accept="image/jpeg,image/jpg,image/png,image/webp,application/pdf"
                    class="hidden"
                    @change="handleFileSelect"
                >
            </div>
            </template>

            {{-- File Preview — replaces upload zone --}}
            <template x-if="file">
            <div class="border border-gray-200 rounded-2xl p-6">
                <div class="flex items-center gap-4">
                    <template x-if="filePreview">
                        <img :src="filePreview" class="w-16 h-16 object-cover rounded-lg border" alt="Preview">
                    </template>

                    <template x-if="!filePreview">
                        <div class="w-16 h-16 rounded-lg bg-red-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </template>

                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-gray-800 truncate" x-text="fileName"></p>
                        <p class="text-sm text-gray-500" x-text="fileSize"></p>
                    </div>

                    <button
                        @click.stop="removeFile()"
                        class="text-gray-400 hover:text-red-500 transition-colors p-1"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
            </div>
            </template>

            {{-- Requirements Checklist --}}
            <div class="mt-6 border border-gray-200 rounded-xl p-5 space-y-3">
                <p class="text-sm font-semibold text-gray-700 mb-1">Requisitos para el análisis</p>

                <div class="flex items-center gap-3 text-sm">
                    <template x-if="idOcupacion">
                        <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" stroke-width="2"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 13l3 3 7-7"/>
                        </svg>
                    </template>
                    <template x-if="!idOcupacion">
                        <svg class="w-5 h-5 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" stroke-width="2"/>
                        </svg>
                    </template>
                    <span :class="idOcupacion ? 'text-green-700' : 'text-gray-500'">Ocupación seleccionada</span>
                </div>

                <div class="flex items-center gap-3 text-sm">
                    <template x-if="fechaNacimiento">
                        <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" stroke-width="2"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 13l3 3 7-7"/>
                        </svg>
                    </template>
                    <template x-if="!fechaNacimiento">
                        <svg class="w-5 h-5 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" stroke-width="2"/>
                        </svg>
                    </template>
                    <span :class="fechaNacimiento ? 'text-green-700' : 'text-gray-500'">Fecha de nacimiento registrada</span>
                </div>

                <div class="flex items-center gap-3 text-sm">
                    <template x-if="file">
                        <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" stroke-width="2"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 13l3 3 7-7"/>
                        </svg>
                    </template>
                    <template x-if="!file">
                        <svg class="w-5 h-5 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" stroke-width="2"/>
                        </svg>
                    </template>
                    <span :class="file ? 'text-green-700' : 'text-gray-500'">Receta cargada</span>
                </div>
            </div>

            {{-- Validation Errors --}}
            <div x-show="validationErrors.length" x-transition class="mt-4">
                <div class="bg-red-50 border border-red-200 rounded-xl p-4 space-y-1">
                    <template x-for="err in validationErrors" :key="err">
                        <div class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            <p class="text-sm text-red-700" x-text="err"></p>
                        </div>
                    </template>
                </div>
            </div>

            {{-- File format error --}}
            <div x-show="errorMessage && step === 'upload'" x-transition class="mt-4">
                <div class="bg-red-50 border border-red-200 rounded-xl p-4 flex items-start gap-3">
                    <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-sm text-red-700" x-text="errorMessage"></p>
                </div>
            </div>

            {{-- Analyze Button — always visible, disabled until complete --}}
            <button
                @click="startAnalysis()"
                :disabled="!canAnalyze"
                :class="canAnalyze
                    ? 'bg-red-600 hover:bg-red-700 text-white cursor-pointer'
                    : 'bg-gray-200 text-gray-400 cursor-not-allowed'"
                class="mt-6 w-full py-3.5 font-semibold rounded-xl transition-colors duration-200 flex items-center justify-center gap-2"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.455 2.456L21.75 6l-1.036.259a3.375 3.375 0 00-2.455 2.456z" />
                </svg>
                Analizar receta
            </button>

        </div>

        {{-- Loading Step --}}
        <div x-show="step === 'loading'" class="p-6 md:p-8">
            <div class="flex flex-col items-center justify-center py-14 gap-6">
                {{-- Spinner --}}
                <div class="relative w-24 h-24">
                    <div class="absolute inset-0 border-4 border-red-100 rounded-full"></div>
                    <div class="absolute inset-0 border-4 border-transparent border-t-red-600 rounded-full animate-spin"></div>
                    <div class="absolute inset-2 border-4 border-transparent border-t-red-400 rounded-full animate-spin" style="animation-duration: 1.5s;"></div>
                </div>

                <div class="text-center">
                    <p class="text-xl font-semibold text-gray-800 mb-2">Analizando tu receta</p>
                    <p class="text-sm text-gray-500 max-w-xs mx-auto">
                        Nuestra IA está examinando tu receta para extraer los valores ópticos y generar recomendaciones.
                    </p>
                </div>

                {{-- Fake progress steps --}}
                <div class="w-full max-w-sm space-y-2 mt-4">
                    <div class="flex items-center gap-3 text-sm text-gray-600">
                        <div class="w-5 h-5 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-3 h-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <span>Procesando imagen</span>
                    </div>
                    <div x-show="true" x-transition.delay.500ms class="flex items-center gap-3 text-sm text-gray-600">
                        <div class="w-5 h-5 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-3 h-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <span>Extrayendo valores ópticos</span>
                    </div>
                    <div x-show="true" x-transition.delay.1000ms class="flex items-center gap-3 text-sm text-gray-600">
                        <div class="w-5 h-5 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-3 h-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <span>Generando recomendaciones</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Error Step --}}
        <div x-show="step === 'error'" class="p-6 md:p-8">
            <div class="flex flex-col items-center justify-center py-12 gap-6">
                <div class="w-20 h-20 rounded-full bg-red-100 flex items-center justify-center">
                    <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                </div>

                <div class="text-center">
                    <p class="text-xl font-semibold text-gray-800 mb-2">Error en el análisis</p>
                    <p class="text-sm text-gray-500 max-w-sm mx-auto" x-text="errorMessage"></p>
                </div>

                <div class="flex gap-3">
                    <button
                        @click="tryAgain()"
                        class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl transition-colors"
                    >
                        Intentar de nuevo
                    </button>
                    <button
                        @click="show = false; resetState()"
                        class="px-6 py-2.5 border border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition-colors"
                    >
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
        </div>

        {{-- Scrollable step (result) --}}
        <div x-show="step === 'result'" class="flex-1 min-h-0 overflow-y-auto rounded-b-3xl">
        <div x-show="step === 'result'" x-transition class="p-6 md:p-8">
            <x-optical-form />
        </div>
        </div>

    </div>
</div>
