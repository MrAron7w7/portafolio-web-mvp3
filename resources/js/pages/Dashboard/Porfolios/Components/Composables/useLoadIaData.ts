// ============================================
// composables/useLoadIAData.ts - ACTUALIZADO
// Con mapeo de personal.summary
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
            // INFORMACIÓN PERSONAL - SUMMARY
            // ============================================
            if (iaData.personal && Array.isArray(iaData.personal) && iaData.personal.length > 0) {
                const personalData = iaData.personal[0];
                
                if (personalData.summary) {
                    formData.personal.summary = personalData.summary;
                    console.log('✅ [MAPPING] Personal summary mapeado');
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

            // ============================================
            // REDES SOCIALES
            // ============================================
            if (iaData.personal) {
                formData.social = {
                    linkedin: iaData.personal.linkedin || '',
                    github: iaData.personal.github || '',
                    website: iaData.personal.website || '',
                };
                console.log('✅ [MAPPING] Redes sociales mapeadas');
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