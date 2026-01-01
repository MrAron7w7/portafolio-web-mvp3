// ============================================
// useAIAnalysis.ts - CON DEBUGGING INTEGRADO + NUEVO MAPA
// ============================================

import { ref, computed } from 'vue';

// ============================================
// TIPOS - NUEVOS MAPEADOS CON LA ESTRUCTURA REAL
// ============================================

interface Experience {
    company: string;
    position: string;
    startDate: string;
    endDate: string;
    current: boolean;
    description: string;
}

interface Education {
    id: number;
    degree: string;
    institution: string;
    startDate: string;
    endDate: string;
    description: string | null;
    current: boolean;
}

interface Language {
    id: number;
    name: string;
    level: string;
}

interface Project {
    id: number;
    name: string;
    description: string;
    image: string | null;
    link: string | null;
    technologies: string[];
}

interface Skill {
    name: string;
    level: number;
}

interface Skills {
    technical: Skill[];
    soft: Skill[];
}

interface PersonalInfo {
    firstName?: string;
    lastName?: string;
    title?: string;
    summary: string;
    email?: string;
    phone?: string;
    city?: string;
    country?: string;
    linkedin?: string;
    github?: string;
    website?: string;
}

// Respuesta exacta del endpoint IA
interface IAEndpointResponse {
    personal: PersonalInfo[];
    experience: Experience[];
    skills: Skills;
    projects: Project[];
    education: Education[];
    languages: Language[];
}

// Exportar para usar en componentes
export interface AIAnalysisResult {
    personal: PersonalInfo[];
    experience: Experience[];
    skills: Skills;
    projects: Project[];
    education: Education[];
    languages: Language[];
}

// ============================================
// COMPOSABLE
// ============================================

export const useAIAnalysis = () => {
    // ============================================
    // ESTADO - MODALES
    // ============================================
    
    const showDescriptionModal = ref(false);
    const showResultsModal = ref(false);
    const showConfirmationModal = ref(false);

    // ============================================
    // ESTADO - ANÁLISIS
    // ============================================
    
    const isAnalyzing = ref(false);
    const isAutoCompleting = ref(false);
    const userDescription = ref('');
    const analysisResult = ref<AIAnalysisResult | null>(null);
    const errorMessage = ref('');

    // ============================================
    // ACCIONES - MODALES
    // ============================================

    const openDescriptionModal = () => {
        console.log('📝 Abriendo modal de descripción...');
        showDescriptionModal.value = true;
    };

    const closeDescriptionModal = () => {
        console.log('❌ Cerrando modal de descripción');
        showDescriptionModal.value = false;
    };

    const openResultsModal = () => {
        console.log('📊 Abriendo modal de resultados...');
        showResultsModal.value = true;
    };

    const closeResultsModal = () => {
        console.log('❌ Cerrando modal de resultados');
        showResultsModal.value = false;
    };

    const openConfirmationModal = () => {
        console.log('✅ Abriendo modal de confirmación...');
        showConfirmationModal.value = true;
    };

    const closeConfirmationModal = () => {
        console.log('❌ Cerrando modal de confirmación');
        showConfirmationModal.value = false;
    };

    // ============================================
    // FUNCIONES AUXILIARES
    // ============================================

    /**
     * Convierte nivel numérico (0-100) a texto
     */
    const convertLevelToText = (level: number): string => {
        if (level >= 86) return 'Experto';
        if (level >= 71) return 'Avanzado';
        if (level >= 41) return 'Intermedio';
        return 'Básico';
    };

    /**
     * ✨ Parsea la respuesta del endpoint
     * El endpoint devuelve: { summary: '{"personal": [], ...}', raw: {...}, success: true }
     */
    const parseEndpointResponse = (response: any): IAEndpointResponse => {
        console.log('🔍 [PARSE] Parseando respuesta del endpoint...');
        
        try {
            // ✨ IMPORTANTE: El JSON está en response.summary como STRING
            if (response?.summary && typeof response.summary === 'string') {
                console.log('📝 [PARSE] response.summary es STRING JSON');
                
                try {
                    // Parse del string JSON
                    const parsed = JSON.parse(response.summary);
                    console.log('✅ [PARSE] JSON parseado correctamente');
                    console.log('   Estructura:', {
                        personal: parsed.personal?.length || 0,
                        experience: parsed.experience?.length || 0,
                        technical: parsed.skills?.technical?.length || 0,
                        soft: parsed.skills?.soft?.length || 0,
                        projects: parsed.projects?.length || 0,
                        education: parsed.education?.length || 0,
                        languages: parsed.languages?.length || 0
                    });
                    
                    return parsed as IAEndpointResponse;
                } catch (parseError) {
                    console.error('❌ [PARSE] Error parseando JSON string:', parseError);
                    throw parseError;
                }
            }
            
            // Fallback: Si ya es objeto
            if (response?.summary && typeof response.summary === 'object') {
                console.log('✅ [PARSE] response.summary ya es objeto');
                return response.summary as IAEndpointResponse;
            }

            // Fallback 2: Si la respuesta es directamente el objeto
            if (response?.personal && response?.experience && response?.skills) {
                console.log('✅ [PARSE] Response es objeto directo (sin envolvimiento)');
                return response as IAEndpointResponse;
            }
            
            throw new Error('No se encontró estructura válida en la respuesta');
            
        } catch (error) {
            console.error('❌ [PARSE] Error al parsear:', error);
            throw error;
        }
    };

    /**
     * ✅ Valida que la respuesta tenga los datos necesarios
     */
    const validateResponse = (response: IAEndpointResponse): boolean => {
        console.log('✔️ [VALIDATE] Validando estructura...');
        
        const checks = {
            personal: Array.isArray(response.personal) && response.personal.length > 0,
            experience: Array.isArray(response.experience),
            skills: response.skills && 
                    Array.isArray(response.skills.technical) && 
                    Array.isArray(response.skills.soft),
            projects: Array.isArray(response.projects),
            education: Array.isArray(response.education),
            languages: Array.isArray(response.languages)
        };

        console.log('   Validaciones:', checks);

        const isValid = Object.values(checks).every(check => check);
        
        if (!isValid) {
            console.warn('⚠️ [VALIDATE] Algunos campos están vacíos, pero continuando...');
        }

        return true; // Retornar true aunque haya campos vacíos
    };

    /**
     * Intenta extraer Toda la información personal del texto (fallback)
     */
    const extractInfoFromText = (text: string): { 
        firstName?: string, 
        lastName?: string, 
        title?: string,
        email?: string,
        phone?: string,
        city?: string,
        country?: string,
        linkedin?: string,
        github?: string,
        website?: string
    } => {
        const result: any = {};

        // 1. NOMBRES (Búsqueda más precisa de la identidad)
        const namePatterns = [
            /(?:Soy|soy|nombre es|llamo)\s+([A-ZÁÉÍÓÚÑ][a-záéíóúñ]+(?:\s+[A-ZÁÉÍÓÚÑ][a-záéíóúñ]+){1,3})/,
            /([A-ZÁÉÍÓÚÑ][a-záéíóúñ]+(?:\s+[A-ZÁÉÍÓÚÑ][a-záéíóúñ]+){1,3})/
        ];

        let fullNameFound = "";
        for (const pattern of namePatterns) {
            const match = text.match(pattern);
            if (match) {
                fullNameFound = match[1].trim();
                const parts = fullNameFound.split(/\s+/);
                result.firstName = parts[0];
                result.lastName = parts.slice(1).join(' ');
                break;
            }
        }

        // 2. TÍTULO PROFESIONAL (Evitando capturar el nombre)
        // Buscamos patrones que sigan al nombre o que usen "un/una/como"
        const titlePatterns = [
            // Patrón: "Nombre, un [Título]" o "Nombre, [Título] radicado en"
            new RegExp(`(?:${result.firstName}|${result.lastName}|${fullNameFound})\\s*,?\\s+(?:un|una|el|la)?\\s*([A-ZÁÉÍÓÚÑ][a-záéíóúñ]+(?:\\s+(?:de|en|para|y|Cloud|IT|Sr|Jr|Lead|Manager)\\s+|\\s+[A-ZÁÉÍÓÚÑ][a-záéíóúñ]+\\s*){0,6})`, 'i'),
            // Patrón general: "Soy [Título]" o "Trabajo como [Título]" excluyendo el nombre ya encontrado
            /(?:Trabajo como|como|Cargo:|Puesto:)\s+(?:un|una|el|la)?\s*([A-ZÁÉÍÓÚÑ][a-záéíóúñ]+(?:\s+(?:de|en|para|y|Cloud|IT|Sr|Jr|Lead|Manager)\s+|\s+[A-ZÁÉÍÓÚÑ][a-záéíóúñ]+\s*){0,6})/i,
            /(?:Soy|soy)\s+(?:un|una|el|la)\s+([A-ZÁÉÍÓÚÑ][a-záéíóúñ]+(?:\s+(?:de|en|para|y|Cloud|IT|Sr|Jr|Lead|Manager)\s+|\s+[A-ZÁÉÍÓÚÑ][a-záéíóúñ]+\s*){0,6})/i
        ];

        for (const pattern of titlePatterns) {
            const match = text.match(pattern);
            if (match) {
                const potentialTitle = match[1].trim();
                // Validar que el título no sea el mismo nombre, apellido ni nombre completo
                if (potentialTitle !== result.firstName && 
                    potentialTitle !== result.lastName && 
                    potentialTitle !== fullNameFound &&
                    !fullNameFound.includes(potentialTitle)) {
                    result.title = potentialTitle;
                    break;
                }
            }
        }

        // 3. CONTACTO (RESTAURADO)
        const emailPattern = /([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})/;
        const phonePattern = /(?:\+?(\d{1,3}))?[-. (]*(\d{3})[-. )]*(\d{3})[-. ]*(\d{4,6})/;
        
        // 4. UBICACIÓN (Ciudad, País)
        const locationPatterns = [
            /(?:Vivo en|ubicado en|Resido en|Desde|en)\s+([A-ZÁÉÍÓÚÑ][a-záéíóúñ]+)(?:,?\s+([A-ZÁÉÍÓÚÑ][a-záéíóúñ]+))?/,
        ];

        // 5. REDES SOCIALES
        const socialPatterns = {
            linkedin: /(?:linkedin\.com\/in\/|linkedin:)\s*([a-zA-Z0-9-ñÑ\._]+)/i,
            github: /(?:github\.com\/|github:)\s*([a-zA-Z0-9-ñÑ\._]+)/i,
            website: /(?:sitio web|portfolio|web:)\s*(https?:\/\/[^\s]+)/i
        };

        const emailMatch = text.match(emailPattern);
        if (emailMatch) result.email = emailMatch[1];

        const phoneMatch = text.match(phonePattern);
        if (phoneMatch) result.phone = phoneMatch[0].trim();

        for (const pattern of locationPatterns) {
            const match = text.match(pattern);
            if (match) {
                result.city = match[1].trim();
                if (match[2]) result.country = match[2].trim();
                break;
            }
        }

        if (socialPatterns.linkedin.test(text)) {
            const m = text.match(socialPatterns.linkedin);
            if (m) {
                const val = m[1];
                result.linkedin = val.startsWith('http') ? val : `https://linkedin.com/in/${val}`;
            }
        }
        if (socialPatterns.github.test(text)) {
            const m = text.match(socialPatterns.github);
            if (m) {
                const val = m[1];
                result.github = val.startsWith('http') ? val : `https://github.com/${val}`;
            }
        }
        if (socialPatterns.website.test(text)) {
            const m = text.match(socialPatterns.website);
            if (m) result.website = m[1];
        }
        
        return result;
    };

    /**
     * Crea un summary automático basado en los datos
     */
    const generateSummary = (response: IAEndpointResponse): string => {
        console.log('📝 [SUMMARY] Generando resumen automático...');

        const {
            experience = [],
            skills = { technical: [], soft: [] },
            education = [],
            languages = [],
            projects = []
        } = response;

        const experienceText = experience.length > 0
            ? `${experience.length} posición${experience.length !== 1 ? 'es' : ''} profesional${experience.length !== 1 ? 'es' : ''}`
            : 'sin experiencia registrada';

        const skillsText = (skills.technical?.length || 0) + (skills.soft?.length || 0);
        const educationText = education.length > 0
            ? `${education.length} formación${education.length !== 1 ? 'es' : ''} académica${education.length !== 1 ? 's' : ''}`
            : 'sin educación registrada';

        const languagesText = languages.length > 0
            ? `${languages.length} idioma${languages.length !== 1 ? 's' : ''}`
            : 'sin idiomas registrados';

        const projectsText = projects.length > 0
            ? `${projects.length} proyecto${projects.length !== 1 ? 's' : ''}`
            : 'sin proyectos registrados';

        const summary = `Se ha analizado tu perfil profesional identificando ${experienceText}, ${skillsText} habilidades en total, ${educationText}, ${languagesText} y ${projectsText}. Tu experiencia y formación abarcan múltiples áreas de desarrollo y especialización.`;

        console.log('✅ [SUMMARY] Resumen generado:', summary);

        return summary;
    };

    /**
     * Asegura que los datos tengan valores por defecto
     */
    const ensureDefaults = (response: IAEndpointResponse): IAEndpointResponse => {
        console.log('🛡️ [DEFAULTS] Aplicando valores por defecto...');

        // Intentar extraer nombre del texto si no viene en el JSON
        const extracted = extractInfoFromText(userDescription.value);
        const personal = (response.personal && response.personal.length > 0 ? response.personal[0] : {}) as any;

        // Log detallado para debugging
        console.log('📋 [DEFAULTS] Datos personales de IA:', {
            firstName: personal.firstName,
            lastName: personal.lastName,
            title: personal.title,
            position: personal.position, // Por si viene así
            name: personal.name
        });
        console.log('📋 [DEFAULTS] Datos extraídos por regex:', {
            firstName: extracted.firstName,
            lastName: extracted.lastName,
            title: extracted.title
        });

        // Si viene un campo 'name' lo intentamos dividir
        if (personal.name && !personal.firstName && !personal.lastName) {
            const parts = personal.name.trim().split(/\s+/);
            personal.firstName = parts[0];
            personal.lastName = parts.slice(1).join(' ');
            console.log('✨ [DEFAULTS] Nombre dividido desde campo "name":', { firstName: personal.firstName, lastName: personal.lastName });
        }

        // Determinar el título final con validación
        let finalTitle = personal.title || personal.position || '';
        const finalFirstName = personal.firstName || extracted.firstName || '';
        const finalLastName = personal.lastName || extracted.lastName || '';
        const fullName = `${finalFirstName} ${finalLastName}`.trim();

        // Validación: Si el título de la IA es en realidad el nombre/apellido, usar el extraído
        if (finalTitle === finalFirstName || 
            finalTitle === finalLastName || 
            finalTitle === fullName ||
            fullName.includes(finalTitle)) {
            console.warn('⚠️ [DEFAULTS] La IA devolvió el nombre como título, usando fallback regex');
            finalTitle = extracted.title || '';
        }

        // Si aún no hay título, intentar extraer más agresivamente
        if (!finalTitle && userDescription.value) {
            const titleMatch = userDescription.value.match(/(?:un|una|el|la)\s+([A-ZÁÉÍÓÚÑ][a-záéíóúñ]+(?:\s+(?:de|en|para|y|Cloud|IT|Senior|Junior|Lead|Manager|Developer|Desarrollador|Arquitecto|Ingeniero|Diseñador|Analista)\s*){1,6})/i);
            if (titleMatch) {
                finalTitle = titleMatch[1].trim();
                console.log('✨ [DEFAULTS] Título extraído por búsqueda agresiva:', finalTitle);
            }
        }

        console.log('✅ [DEFAULTS] Título final:', finalTitle);

        return {
            personal: [{
                summary: personal.summary || '',
                firstName: finalFirstName,
                lastName: finalLastName,
                title: finalTitle,
                email: personal.email || extracted.email || '',
                phone: personal.phone || extracted.phone || '',
                city: personal.city || personal.location?.split(',')?.[0]?.trim() || extracted.city || '',
                country: personal.country || personal.location?.split(',')?.[1]?.trim() || extracted.country || '',
                linkedin: personal.linkedin || extracted.linkedin || '',
                github: personal.github || extracted.github || '',
                website: personal.website || extracted.website || '',
            }],
            experience: (response.experience || []).map(exp => ({
                company: exp.company || 'Sin empresa',
                position: exp.position || 'Sin título',
                startDate: exp.startDate || '',
                endDate: exp.endDate || '',
                current: exp.current || false,
                description: exp.description || ''
            })),
            skills: {
                technical: (response.skills?.technical || []).map(skill => ({
                    name: skill.name || 'Habilidad desconocida',
                    level: skill.level || 0
                })),
                soft: (response.skills?.soft || []).map(skill => ({
                    name: skill.name || 'Habilidad desconocida',
                    level: skill.level || 0
                }))
            },
            projects: (response.projects || []).map(proj => ({
                id: proj.id || Math.random(),
                name: proj.name || 'Proyecto sin nombre',
                description: proj.description || '',
                image: proj.image || null,
                link: proj.link || null,
                technologies: proj.technologies || []
            })),
            education: (response.education || []).map(edu => ({
                id: edu.id || Math.random(),
                degree: edu.degree || 'Grado desconocido',
                institution: edu.institution || 'Institución desconocida',
                startDate: edu.startDate || '',
                endDate: edu.endDate || '',
                description: edu.description || null,
                current: edu.current || false
            })),
            languages: (response.languages || []).map(lang => ({
                id: lang.id || Math.random(),
                name: lang.name || 'Idioma desconocido',
                level: lang.level || 'Sin nivel'
            }))
        };
    };

    // ============================================
    // FUNCIONES - ANÁLISIS IA
    // ============================================

    /**
     * Analiza la descripción con IA usando el endpoint real
     * CON DEBUGGING COMPLETO Y NUEVO MAPA
     */
    const analyzeWithAI = async (rawResponse: any): Promise<void> => {
        console.log('🚀 [ANALYZE] Iniciando análisis con respuesta del servidor...');
        console.log('   Respuesta recibida:', rawResponse);

        try {
            // 1️⃣ Parse
            const parsedResponse = parseEndpointResponse(rawResponse);
            console.log('✅ [ANALYZE] Respuesta parseada');

            // 2️⃣ Validar
            validateResponse(parsedResponse);
            console.log('✅ [ANALYZE] Respuesta validada');

            // 3️⃣ Aplicar defaults
            const withDefaults = ensureDefaults(parsedResponse);
            console.log('✅ [ANALYZE] Valores por defecto aplicados');

            // 4️⃣ Generar resumen si está vacío
            if (!withDefaults.personal[0]?.summary) {
                withDefaults.personal[0].summary = generateSummary(withDefaults);
            }
            console.log('✅ [ANALYZE] Resumen completado');

            // 5️⃣ Asignar resultado
            analysisResult.value = withDefaults;
            console.log('✅ [ANALYZE] Resultado asignado');

            // 6️⃣ Abrir modal
            openResultsModal();
            console.log('✅ [ANALYZE] Modal abierto');

        } catch (error) {
            console.error('❌ [ANALYZE] Error en análisis:', error);
            errorMessage.value = 'Error al analizar el perfil. Por favor intenta de nuevo.';
            throw error;
        }
    };

    // ============================================
    // FUNCIONES - AUTOCOMPLETAR
    // ============================================

    /**
     * Autocompletador mejorado que mapea datos correctamente
     */
    const autoCompleteFormData = async (formData: any): Promise<boolean> => {
        console.log('⚡ [AUTOCOMPLETE] Iniciando autocompletar...');

        if (!analysisResult.value) {
            console.error('❌ [AUTOCOMPLETE] No hay resultados para autocompletar');
            return false;
        }

        isAutoCompleting.value = true;

        try {
            // Delay simulado
            await new Promise(resolve => setTimeout(resolve, 1000));

            const result = analysisResult.value;

            // 📋 Log de datos mapeados
            console.log('📋 [AUTOCOMPLETE] Datos a completar:');
            console.log('   Personal:', result.personal[0]?.summary?.substring(0, 50) + '...');
            console.log('   Experiencias:', result.experience.length);
            console.log('   Proyectos:', result.projects.length);
            console.log('   Educación:', result.education.length);
            console.log('   Skills técnicas:', result.skills.technical.length);
            console.log('   Skills blandas:', result.skills.soft.length);
            console.log('   Idiomas:', result.languages.length);

            // Cerrar modal de resultados
            closeResultsModal();
            await new Promise(resolve => setTimeout(resolve, 300));

            // Abrir modal de confirmación
            openConfirmationModal();

            console.log('✅ [AUTOCOMPLETE] Completado exitosamente');
            return true;

        } catch (error) {
            console.error('❌ [AUTOCOMPLETE] Error:', error);
            errorMessage.value = 'Error al completar el formulario. Por favor intenta de nuevo.';
            return false;
        } finally {
            isAutoCompleting.value = false;
        }
    };

    // ============================================
    // RESET
    // ============================================

    const reset = () => {
        console.log('🔄 [RESET] Reseteando estado completo...');
        showDescriptionModal.value = false;
        showResultsModal.value = false;
        showConfirmationModal.value = false;
        isAnalyzing.value = false;
        isAutoCompleting.value = false;
        userDescription.value = '';
        analysisResult.value = null;
        errorMessage.value = '';
        console.log('✅ [RESET] Estado limpio');
    };

    // ============================================
    // COMPUTED
    // ============================================

    const hasResults = computed(() => analysisResult.value !== null);

    const resultsSummary = computed(() => {
        if (!analysisResult.value) return null;

        return {
            experienceCount: analysisResult.value.experience.length,
            projectsCount: analysisResult.value.projects.length,
            educationCount: analysisResult.value.education.length,
            skillsCount: 
                (analysisResult.value.skills.technical?.length || 0) +
                (analysisResult.value.skills.soft?.length || 0),
            languagesCount: analysisResult.value.languages.length
        };
    });

    // ============================================
    // RETORNAR COMPOSABLE
    // ============================================

    return {
        // Estado
        showDescriptionModal,
        showResultsModal,
        showConfirmationModal,
        isAnalyzing,
        isAutoCompleting,
        userDescription,
        analysisResult,
        hasResults,
        errorMessage,
        resultsSummary,

        // Métodos - Modales
        openDescriptionModal,
        closeDescriptionModal,
        openResultsModal,
        closeResultsModal,
        openConfirmationModal,
        closeConfirmationModal,

        // Métodos - Análisis
        analyzeWithAI,
        autoCompleteFormData,

        // Métodos - Utilidad
        reset,
    };
};