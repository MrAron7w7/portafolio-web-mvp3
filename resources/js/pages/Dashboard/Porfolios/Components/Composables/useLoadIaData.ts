// ============================================
// composables/useLoadIAData.ts - ACTUALIZADO
// Mapeo completo de datos incluyendo información de contacto y ubicación
// ============================================

import { reactive } from 'vue';

export function useLoadIAData() {
    /**
     * Mapea los datos del JSON de la IA al formData del editor
     * @param iaData - JSON parseado de la IA
     * @param formData - El objeto formData del editor
     */
    const mapIADataToForm = (iaData: any, formData: any) => {
        try {
            if (!iaData) return false;

            console.log('🔄 [MAPPING] Iniciando mapeo de datos IA...');

            // ============================================
            // INFORMACIÓN PERSONAL
            // ============================================
            if (iaData.personal && Array.isArray(iaData.personal) && iaData.personal.length > 0) {
                const personalData = iaData.personal[0];
                
                if (personalData.summary) {
                    formData.personal.summary = personalData.summary;
                    console.log('✅ [MAPPING] Personal summary mapeado');
                }

                if (personalData.firstName) {
                    formData.personal.firstName = personalData.firstName;
                    console.log('✅ [MAPPING] Nombre mapeado:', personalData.firstName);
                }

                if (personalData.lastName) {
                    formData.personal.lastName = personalData.lastName;
                    console.log('✅ [MAPPING] Apellido mapeado:', personalData.lastName);
                }

                if (personalData.title) {
                    formData.personal.title = personalData.title;
                    console.log('✅ [MAPPING] Título profesional mapeado:', personalData.title);
                }

                if (personalData.email) {
                    formData.personal.email = personalData.email;
                    console.log('✅ [MAPPING] Email mapeado:', personalData.email);
                }

                if (personalData.phone) {
                    formData.personal.phone = personalData.phone;
                    console.log('✅ [MAPPING] Teléfono mapeado:', personalData.phone);
                }

                if (personalData.city) {
                    formData.personal.city = personalData.city;
                    console.log('✅ [MAPPING] Ciudad mapeada:', personalData.city);
                }

                if (personalData.country) {
                    formData.personal.country = personalData.country;
                    console.log('✅ [MAPPING] País mapeado:', personalData.country);
                }

                // Sincronizar sociales si vienen en personal
                if (personalData.linkedin) {
                    formData.personal.linkedin = personalData.linkedin;
                    formData.social.linkedin = personalData.linkedin;
                    console.log('✅ [MAPPING] LinkedIn mapeado');
                }
                if (personalData.github) {
                    formData.personal.github = personalData.github;
                    formData.social.github = personalData.github;
                    console.log('✅ [MAPPING] GitHub mapeado');
                }
                if (personalData.website) {
                    formData.personal.website = personalData.website;
                    formData.social.website = personalData.website;
                    console.log('✅ [MAPPING] Website mapeado');
                }
            }

            // ============================================
            // EXPERIENCIA
            // ============================================
            if (iaData.experience && Array.isArray(iaData.experience)) {
                formData.experience = iaData.experience.map((exp: any, index: number) => ({
                    id: index + 1,
                    company: exp.company || '',
                    position: exp.position || '',
                    period: `${exp.startDate} - ${exp.current ? 'Presente' : exp.endDate}`,
                    description: exp.description || '',
                    startDate: exp.startDate || '',
                    endDate: exp.endDate || '',
                    current: exp.current || false,
                }));
                console.log(`✅ [MAPPING] ${formData.experience.length} experiencias mapeadas`);
            }

            // ============================================
            // IDIOMAS
            // ============================================
            if (iaData.languages && Array.isArray(iaData.languages)) {
                formData.languages = iaData.languages.map((lang: any, index: number) => ({
                    id: lang.id || index + 1,
                    name: lang.name || '',
                    level: lang.level || 'Básico',
                }));
                console.log(`✅ [MAPPING] ${formData.languages.length} idiomas mapeados`);
            }

            // ============================================
            // EDUCACIÓN
            // ============================================
            if (iaData.education && Array.isArray(iaData.education)) {
                formData.education = iaData.education.map((edu: any, index: number) => ({
                    id: edu.id || index + 1,
                    degree: edu.degree || '',
                    institution: edu.institution || '',
                    startDate: edu.startDate || '',
                    endDate: edu.endDate || '',
                    description: edu.description || '',
                    current: edu.current || false,
                }));
                console.log(`✅ [MAPPING] ${formData.education.length} educación(es) mapeada(s)`);
            }

            // ============================================
            // HABILIDADES (Skills)
            // ============================================
            if (iaData.skills) {
                // Skills Técnicas
                if (iaData.skills.technical && Array.isArray(iaData.skills.technical)) {
                    formData.skills.technical = iaData.skills.technical.map((skill: any, index: number) => ({
                        id: index + 1,
                        name: skill.name || '',
                        level: skill.level || 50,
                        category: 'Technical',
                    }));
                    console.log(`✅ [MAPPING] ${formData.skills.technical.length} habilidades técnicas mapeadas`);
                }

                // Skills Blandas
                if (iaData.skills.soft && Array.isArray(iaData.skills.soft)) {
                    formData.skills.soft = iaData.skills.soft.map((skill: any, index: number) => ({
                        id: index + 1000,
                        name: skill.name || '',
                        level: skill.level || 50,
                        category: 'Soft',
                    }));
                    console.log(`✅ [MAPPING] ${formData.skills.soft.length} habilidades blandas mapeadas`);
                }
            }

            // ============================================
            // PROYECTOS
            // ============================================
            if (iaData.projects && Array.isArray(iaData.projects)) {
                formData.projects = iaData.projects.map((proj: any, index: number) => ({
                    id: proj.id || index + 1,
                    name: proj.name || '',
                    description: proj.description || '',
                    image: proj.image || null,
                    link: proj.link || null,
                    technologies: proj.technologies || [],
                }));
                console.log(`✅ [MAPPING] ${formData.projects.length} proyectos mapeados`);
            }

            // ============================================
            // CERTIFICACIONES
            // ============================================
            if (iaData.certifications && Array.isArray(iaData.certifications)) {
                formData.certifications = iaData.certifications.map((cert: any, index: number) => ({
                    id: cert.id || index + 1,
                    name: cert.name || '',
                    issuer: cert.issuer || '',
                    date: cert.date || '',
                    link: cert.link || null,
                }));
                console.log(`✅ [MAPPING] ${formData.certifications.length} certificación(es) mapeada(s)`);
            }

            console.log('✅ [MAPPING] Mapeo completado exitosamente');
            return true;

        } catch (error) {
            console.error('❌ [MAPPING] Error al mapear datos de IA:', error);
            return false;
        }
    };

    return {
        mapIADataToForm,
    };
}