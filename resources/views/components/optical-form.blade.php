{{-- Optical Form - uses parent Alpine scope from prescription-flow --}}
<div>

    {{-- File info bar --}}
    <div class="flex items-center gap-3 mb-6 p-3 bg-gray-50 rounded-xl">
        <div class="w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
        </div>
        <div class="min-w-0">
            <p class="font-medium text-gray-800 text-sm truncate" x-text="fileName"></p>
            <p class="text-xs text-gray-500">Análisis completado</p>
        </div>
        <button
            @click="removeFile(); step = 'upload'"
            class="ml-auto text-sm text-red-600 hover:text-red-700 font-medium flex-shrink-0"
        >
            Cambiar archivo
        </button>
    </div>

    {{-- AI Diagnosis Panel --}}
    <template x-if="prescription && prescription.analisis_ia">
        <div class="mb-8 border border-gray-200 rounded-2xl overflow-hidden">
            <div class="bg-gradient-to-r from-gray-50 to-white px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                </svg>
                <h3 class="font-semibold text-gray-800">Diagnóstico y Recomendación</h3>
            </div>

            <div class="p-6 grid md:grid-cols-2 gap-4">
                {{-- Recommended Lens --}}
                <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-xl">
                    <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wider">Lente Recomendado</p>
                        <p class="font-semibold text-gray-800 mt-1" x-text="getFieldValue('analisis_ia.tipo_lente_recomendado', 'No especificado')"></p>
                    </div>
                </div>

                {{-- Complexity --}}
                <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-xl">
                    <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wider">Complejidad</p>
                        <span
                            class="inline-block mt-1 px-2.5 py-0.5 rounded-full text-xs font-semibold"
                            :class="getComplejidadColor(getFieldValue('analisis_ia.nivel_complejidad', 'estandar'))"
                            x-text="getComplejidadLabel(getFieldValue('analisis_ia.nivel_complejidad', 'estandar'))"
                        ></span>
                    </div>
                </div>

                {{-- Flags --}}
                <div class="md:col-span-2 flex flex-wrap gap-2">
                    <template x-if="getFieldValue('analisis_ia.requiere_multifocal', false)">
                        <span class="px-3 py-1.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            Requiere Multifocal
                        </span>
                    </template>
                    <template x-if="getFieldValue('analisis_ia.requiere_alto_indice', false)">
                        <span class="px-3 py-1.5 rounded-full text-xs font-semibold bg-orange-100 text-orange-800 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            Alto Índice
                        </span>
                    </template>
                    <template x-if="getFieldValue('analisis_ia.requiere_reduccion_diametro', false)">
                        <span class="px-3 py-1.5 rounded-full text-xs font-semibold bg-pink-100 text-pink-800 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            Reducción de Diámetro
                        </span>
                    </template>
                    <template x-if="getFieldValue('analisis_ia.requiere_cita', false)">
                        <span class="px-3 py-1.5 rounded-full text-xs font-semibold bg-red-100 text-red-800 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            Requiere Cita
                        </span>
                    </template>
                </div>

                {{-- Observations --}}
                <div x-show="getFieldValue('analisis_ia.observaciones', '')" class="md:col-span-2 p-4 bg-gray-50 rounded-xl">
                    <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Observaciones</p>
                    <p class="text-sm text-gray-700" x-text="getFieldValue('analisis_ia.observaciones', '')"></p>
                </div>
            </div>
        </div>
    </template>

    {{-- Optical Form --}}
    <div x-show="prescription" class="space-y-8">

        {{-- Patient Info --}}
        <section>
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Datos del Paciente
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Nombre</label>
                    <input
                        type="text"
                        x-model="prescription.paciente.nombre"
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-colors bg-gray-50"
                    >
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Edad</label>
                    <input
                        type="text"
                        x-model="prescription.paciente.edad"
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-colors bg-gray-50"
                    >
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Fecha Receta</label>
                    <input
                        type="text"
                        x-model="prescription.paciente.fecha_receta"
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-colors bg-gray-50"
                    >
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Doctor</label>
                    <input
                        type="text"
                        x-model="prescription.paciente.doctor"
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-colors bg-gray-50"
                    >
                </div>
            </div>
        </section>

        {{-- Distance Vision --}}
        <section>
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                Visión Lejos
            </h3>

            <div class="border border-gray-200 rounded-xl overflow-hidden">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-2.5 text-left font-medium text-gray-600 w-20"></th>
                            <th class="px-4 py-2.5 text-left font-medium text-gray-600">Esfera</th>
                            <th class="px-4 py-2.5 text-left font-medium text-gray-600">Cilindro</th>
                            <th class="px-4 py-2.5 text-left font-medium text-gray-600">Eje</th>
                            <th class="px-4 py-2.5 text-left font-medium text-gray-600">DIP</th>
                            <th class="px-4 py-2.5 text-left font-medium text-gray-600">AV</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-t border-gray-100">
                            <td class="px-4 py-2 font-semibold text-gray-800">OD</td>
                            <td class="px-4 py-2"><input type="text" x-model="prescription.vision_lejos.od.esfera" class="w-full px-2.5 py-1.5 border border-gray-200 rounded-lg text-sm bg-white focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-colors"></td>
                            <td class="px-4 py-2"><input type="text" x-model="prescription.vision_lejos.od.cilindro" class="w-full px-2.5 py-1.5 border border-gray-200 rounded-lg text-sm bg-white focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-colors"></td>
                            <td class="px-4 py-2"><input type="text" x-model="prescription.vision_lejos.od.eje" class="w-full px-2.5 py-1.5 border border-gray-200 rounded-lg text-sm bg-white focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-colors"></td>
                            <td class="px-4 py-2"><input type="text" x-model="prescription.vision_lejos.od.dip" class="w-full px-2.5 py-1.5 border border-gray-200 rounded-lg text-sm bg-white focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-colors"></td>
                            <td class="px-4 py-2"><input type="text" x-model="prescription.vision_lejos.od.av" class="w-full px-2.5 py-1.5 border border-gray-200 rounded-lg text-sm bg-white focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-colors"></td>
                        </tr>
                        <tr class="border-t border-gray-100">
                            <td class="px-4 py-2 font-semibold text-gray-800">OI</td>
                            <td class="px-4 py-2"><input type="text" x-model="prescription.vision_lejos.oi.esfera" class="w-full px-2.5 py-1.5 border border-gray-200 rounded-lg text-sm bg-white focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-colors"></td>
                            <td class="px-4 py-2"><input type="text" x-model="prescription.vision_lejos.oi.cilindro" class="w-full px-2.5 py-1.5 border border-gray-200 rounded-lg text-sm bg-white focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-colors"></td>
                            <td class="px-4 py-2"><input type="text" x-model="prescription.vision_lejos.oi.eje" class="w-full px-2.5 py-1.5 border border-gray-200 rounded-lg text-sm bg-white focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-colors"></td>
                            <td class="px-4 py-2"><input type="text" x-model="prescription.vision_lejos.oi.dip" class="w-full px-2.5 py-1.5 border border-gray-200 rounded-lg text-sm bg-white focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-colors"></td>
                            <td class="px-4 py-2"><input type="text" x-model="prescription.vision_lejos.oi.av" class="w-full px-2.5 py-1.5 border border-gray-200 rounded-lg text-sm bg-white focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-colors"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        {{-- Near Vision --}}
        <section>
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                Visión Cerca
            </h3>

            <div class="border border-gray-200 rounded-xl overflow-hidden">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-2.5 text-left font-medium text-gray-600 w-20"></th>
                            <th class="px-4 py-2.5 text-left font-medium text-gray-600">Esfera</th>
                            <th class="px-4 py-2.5 text-left font-medium text-gray-600">Cilindro</th>
                            <th class="px-4 py-2.5 text-left font-medium text-gray-600">Eje</th>
                            <th class="px-4 py-2.5 text-left font-medium text-gray-600">DIP</th>
                            <th class="px-4 py-2.5 text-left font-medium text-gray-600">AV</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-t border-gray-100">
                            <td class="px-4 py-2 font-semibold text-gray-800">OD</td>
                            <td class="px-4 py-2"><input type="text" x-model="prescription.vision_cerca.od.esfera" class="w-full px-2.5 py-1.5 border border-gray-200 rounded-lg text-sm bg-white focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-colors"></td>
                            <td class="px-4 py-2"><input type="text" x-model="prescription.vision_cerca.od.cilindro" class="w-full px-2.5 py-1.5 border border-gray-200 rounded-lg text-sm bg-white focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-colors"></td>
                            <td class="px-4 py-2"><input type="text" x-model="prescription.vision_cerca.od.eje" class="w-full px-2.5 py-1.5 border border-gray-200 rounded-lg text-sm bg-white focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-colors"></td>
                            <td class="px-4 py-2"><input type="text" x-model="prescription.vision_cerca.od.dip" class="w-full px-2.5 py-1.5 border border-gray-200 rounded-lg text-sm bg-white focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-colors"></td>
                            <td class="px-4 py-2"><input type="text" x-model="prescription.vision_cerca.od.av" class="w-full px-2.5 py-1.5 border border-gray-200 rounded-lg text-sm bg-white focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-colors"></td>
                        </tr>
                        <tr class="border-t border-gray-100">
                            <td class="px-4 py-2 font-semibold text-gray-800">OI</td>
                            <td class="px-4 py-2"><input type="text" x-model="prescription.vision_cerca.oi.esfera" class="w-full px-2.5 py-1.5 border border-gray-200 rounded-lg text-sm bg-white focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-colors"></td>
                            <td class="px-4 py-2"><input type="text" x-model="prescription.vision_cerca.oi.cilindro" class="w-full px-2.5 py-1.5 border border-gray-200 rounded-lg text-sm bg-white focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-colors"></td>
                            <td class="px-4 py-2"><input type="text" x-model="prescription.vision_cerca.oi.eje" class="w-full px-2.5 py-1.5 border border-gray-200 rounded-lg text-sm bg-white focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-colors"></td>
                            <td class="px-4 py-2"><input type="text" x-model="prescription.vision_cerca.oi.dip" class="w-full px-2.5 py-1.5 border border-gray-200 rounded-lg text-sm bg-white focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-colors"></td>
                            <td class="px-4 py-2"><input type="text" x-model="prescription.vision_cerca.oi.av" class="w-full px-2.5 py-1.5 border border-gray-200 rounded-lg text-sm bg-white focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-colors"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        {{-- Additional Values --}}
        <section>
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                Valores Adicionales
            </h3>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">ADD OD</label>
                    <input type="text" x-model="prescription.add.od" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm bg-white focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-colors">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">ADD OI</label>
                    <input type="text" x-model="prescription.add.oi" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm bg-white focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-colors">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Prisma OD</label>
                    <input type="text" x-model="prescription.prisma.od" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm bg-white focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-colors">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Prisma OI</label>
                    <input type="text" x-model="prescription.prisma.oi" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm bg-white focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-colors">
                </div>
            </div>
        </section>

        {{-- Contact Lens --}}
        <section>
            <button
                @click="showContactLens = !showContactLens"
                class="flex items-center gap-2 text-gray-500 hover:text-gray-700 transition-colors"
            >
                <svg
                    class="w-4 h-4 transition-transform"
                    :class="showContactLens ? 'rotate-90' : ''"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-sm font-medium">Lentes de Contacto</span>
            </button>

            <div x-show="showContactLens" x-transition class="mt-4 border border-gray-200 rounded-xl overflow-hidden">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-2.5 text-left font-medium text-gray-600 w-20"></th>
                            <th class="px-4 py-2.5 text-left font-medium text-gray-600">Esfera</th>
                            <th class="px-4 py-2.5 text-left font-medium text-gray-600">Cilindro</th>
                            <th class="px-4 py-2.5 text-left font-medium text-gray-600">Eje</th>
                            <th class="px-4 py-2.5 text-left font-medium text-gray-600">CB</th>
                            <th class="px-4 py-2.5 text-left font-medium text-gray-600">Diámetro</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-t border-gray-100">
                            <td class="px-4 py-2 font-semibold text-gray-800">OD</td>
                            <td class="px-4 py-2"><input type="text" x-model="prescription.lente_contacto.od.esfera" class="w-full px-2.5 py-1.5 border border-gray-200 rounded-lg text-sm bg-white focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-colors"></td>
                            <td class="px-4 py-2"><input type="text" x-model="prescription.lente_contacto.od.cilindro" class="w-full px-2.5 py-1.5 border border-gray-200 rounded-lg text-sm bg-white focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-colors"></td>
                            <td class="px-4 py-2"><input type="text" x-model="prescription.lente_contacto.od.eje" class="w-full px-2.5 py-1.5 border border-gray-200 rounded-lg text-sm bg-white focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-colors"></td>
                            <td class="px-4 py-2"><input type="text" x-model="prescription.lente_contacto.od.cb" class="w-full px-2.5 py-1.5 border border-gray-200 rounded-lg text-sm bg-white focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-colors"></td>
                            <td class="px-4 py-2"><input type="text" x-model="prescription.lente_contacto.od.diametro" class="w-full px-2.5 py-1.5 border border-gray-200 rounded-lg text-sm bg-white focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-colors"></td>
                        </tr>
                        <tr class="border-t border-gray-100">
                            <td class="px-4 py-2 font-semibold text-gray-800">OI</td>
                            <td class="px-4 py-2"><input type="text" x-model="prescription.lente_contacto.oi.esfera" class="w-full px-2.5 py-1.5 border border-gray-200 rounded-lg text-sm bg-white focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-colors"></td>
                            <td class="px-4 py-2"><input type="text" x-model="prescription.lente_contacto.oi.cilindro" class="w-full px-2.5 py-1.5 border border-gray-200 rounded-lg text-sm bg-white focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-colors"></td>
                            <td class="px-4 py-2"><input type="text" x-model="prescription.lente_contacto.oi.eje" class="w-full px-2.5 py-1.5 border border-gray-200 rounded-lg text-sm bg-white focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-colors"></td>
                            <td class="px-4 py-2"><input type="text" x-model="prescription.lente_contacto.oi.cb" class="w-full px-2.5 py-1.5 border border-gray-200 rounded-lg text-sm bg-white focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-colors"></td>
                            <td class="px-4 py-2"><input type="text" x-model="prescription.lente_contacto.oi.diametro" class="w-full px-2.5 py-1.5 border border-gray-200 rounded-lg text-sm bg-white focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-colors"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        {{-- Indications --}}
        <section>
            <label class="block text-sm font-semibold text-gray-800 mb-2">Indicaciones</label>
            <textarea
                x-model="prescription.indicaciones"
                rows="3"
                class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-colors resize-none"
            ></textarea>
        </section>

        {{-- Actions --}}
        <div class="flex flex-col sm:flex-row-reverse gap-3 pt-4 border-t border-gray-100">
            <button
                @click="show = false; resetState(); window.location.href = '#catalogo'"
                class="w-full sm:w-auto px-8 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl transition-colors duration-200 flex items-center justify-center gap-2"
            >
                Continuar al catálogo
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
            </button>

            <button
                @click="show = false; resetState()"
                class="w-full sm:w-auto px-8 py-3 border border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition-colors"
            >
                Cerrar sin guardar
            </button>
        </div>

    </div>

</div>
