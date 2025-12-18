<script setup lang="ts">
    import { onMounted } from 'vue';
    import { useAboutValidation, type AboutData } from './Composables/useAboutValidation';
    
    const props = defineProps<{
        modelValue: AboutData;
        validation: any;
    }>();
    
    const emit = defineEmits<{
        'update:modelValue': [value: AboutData];
    }>();
    
    const {
        errors: aboutErrors,
        validateField,
        getErrorClass,
        getCharCount,
    } = props.validation;
    
    onMounted(() => {
        props.validation.clearErrors();
    });
    
    // Actualizar campo + validar
    const updateField = (field: keyof AboutData, value: string) => {
        // Marcar como tocado al escribir
        props.validation.markAsTouched(field);
    
        const updated = { ...props.modelValue, [field]: value };
        emit('update:modelValue', updated);
    
        // Validar en tiempo real (forceShow = false)
        if (aboutErrors[field]) {
            validateField(field, value, false);
        }
    };
    </script>
    
    <template>
        <div>
            <div class="mb-8">
                <h1 class="mb-3 text-2xl font-bold text-gray-900 lg:text-3xl">Sobre Mí</h1>
                <p class="text-lg text-gray-600">
                    Cuéntanos un poco más sobre ti, tu trayectoria y tus objetivos.
                </p>
            </div>
    
            <div class="space-y-6">
                <!-- Resumen -->
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Resumen Profesional *</label>
                    <p class="mb-2 text-xs text-gray-500">
                        Este texto aparecerá en la cabecera o sección principal.
                    </p>
                    <textarea
                        :value="modelValue.summary"
                        @input="updateField('summary', ($event.target as HTMLTextAreaElement).value)"
                        rows="20"
                        class="w-full rounded-lg border bg-gray-50 px-3 py-2 text-base text-gray-900 transition-colors focus:outline-none focus:ring-2 focus:ring-[#005aeb]/20"
                        :class="getErrorClass('summary', false)"
                        placeholder="Soy un desarrollador apasionado con experiencia en..."
                    ></textarea>
                    <div class="mt-1 flex items-center justify-between">
                        <span
                            v-if="aboutErrors.summary"
                            class="text-sm text-red-500"
                        >
                            {{ aboutErrors.summary }}
                        </span>
                        <span v-else class="text-sm text-transparent">placeholder</span>
                        <span
                            class="text-xs"
                            :class="[
                                getCharCount(modelValue.summary, 'summary').isOverLimit
                                    ? 'font-medium text-red-500'
                                    : getCharCount(modelValue.summary, 'summary').isNearLimit
                                      ? 'text-amber-500'
                                      : 'text-gray-400',
                            ]"
                        >
                            {{ getCharCount(modelValue.summary, 'summary').current }}/{{
                                getCharCount(modelValue.summary, 'summary').max
                            }}
                        </span>
                    </div>
                </div>
    
             
            </div>
        </div>
    </template>