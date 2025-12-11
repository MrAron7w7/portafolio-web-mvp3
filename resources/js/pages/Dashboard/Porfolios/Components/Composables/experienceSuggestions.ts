// ==========================================
// CON FALLBACK GENÉRICO
// VERSIÓN: ESPAÑOL
// ==========================================

import type { Experience } from './useExperienceValidation';

/**
 * Retorna sugerencias ESPECÍFICAS según el cargo y tipo de trabajo
 * Si no coincide con ningún rol conocido, devuelve sugerencias GENÉRICAS
 */
export const getDetailedDescriptionSuggestions = (experience: Experience) => {
    const position = (experience.position || '').toLowerCase();
    
    // ============================================
    // DESARROLLADORES / INGENIEROS
    // ============================================
    if (position.includes('developer') || position.includes('engineer') || position.includes('programador') || position.includes('software') || position.includes('desarrollador')) {
        return [
            {
                label: '🎨 Frontend',
                text: `Diseñé e implementé interfaces modernas con React/Vue que cautivaron a usuarios. Componentes reutilizables con TypeScript garantizando calidad de código. Optimicé Core Web Vitals logrando carga instantánea. Tests automatizados con 85%+ cobertura asegurando confiabilidad.`
            },
            {
                label: '⚙️ Backend',
                text: `Construí APIs robustas y escalables que soportan millones de requests. Diseñé bases de datos optimizadas en PostgreSQL/MongoDB con performance excepcional. Implementé autenticación segura y control de acceso granular. Reduje tiempos de respuesta en 60% mediante optimización inteligente de queries.`
            },
            {
                label: '🔗 Full Stack',
                text: `Desarrollé soluciones end-to-end completas, desde arquitectura de datos hasta interfaces pixel-perfect. Stack moderno: [Frontend]/[Backend]/[Base de Datos]. Automaticé deployments con CI/CD en GitHub Actions logrando releases sin fricción. Monitoreo proactivo en New Relic previniendo incidentes.`
            },
            {
                label: '🗄️ Base de Datos',
                text: `Architecuté esquemas escalables que crecen sin límites. Índices estratégicamente posicionados multiplicaron performance en 70%. Implementé replicación y backups automáticos asegurando cero pérdida de datos. Migraciones sin downtime en producción manteniendo 99.99% uptime.`
            },
            {
                label: '🚀 DevOps',
                text: `Containernicé toda la infraestructura con Docker para consistencia total. Orquestación en Kubernetes escalando automáticamente según demanda. Infraestructura como código con Terraform reduciendo configuración manual 95%. Pipelines CI/CD que despliegan cambios en segundos.`
            },
            {
                label: '✅ Testing',
                text: `Automaticé suite completa de tests garantizando calidad inquebrantable. Testing end-to-end con Cypress capturando bugs antes de producción. Unit tests exhaustivos con Jest alcanzando 90%+ cobertura. Reduje defectos en producción en 75% mediante testing disciplinado.`
            },
            {
                label: '🏗️ Arquitectura',
                text: `Diseñé arquitecturas escalables que crecen sin refactores dolorosos. Patrones bien aplicados (MVC, MVVM, Clean Architecture) simplificando mantenimiento. Microservicios basados en eventos para desacoplamiento perfecto. Code reviews exhaustivos elevando estándar técnico del equipo.`
            },
            {
                label: '⚡ Performance',
                text: `Optimicé bundles JavaScript reduciendo tamaño en 40%. Code splitting inteligente y lazy loading acelerando carga inicial. Estrategias de caching sofisticadas con Redis multiplicando velocidad. Transformé sitios lentos en experiencias relámpago: de 5s a 1.2s.`
            },
            {
                label: '🔐 Seguridad',
                text: `Implementé defensas robustas contra OWASP Top 10 protegiendo datos críticos. Prevención de XSS/CSRF eliminando vectores de ataque comunes. Encriptación de datos en reposo y en tránsito. Auditorías de seguridad regulares alcanzando cumplimiento PCI-DSS.`
            },
            {
                label: '👨‍🏫 Mentoría',
                text: `Mentoricé [N] developers juniors transformándolos en profesionales competentes. Code reviews constructivos que elevan habilidades del equipo. Pair programming sessions despejando bloqueos técnicos. Documentación técnica cristalina facilitando onboarding rápido.`
            }
        ];
    }
    
    // ============================================
    // DISEÑADORES / UX-UI
    // ============================================
    if (position.includes('diseñador') || position.includes('designer') || position.includes('uxui') || position.includes('ux') || position.includes('ui') || position.includes('diseño')) {
        return [
            {
                label: '🎨 Diseño Visual',
                text: `Creé design system completo con 150+ componentes coherentes reduciendo inconsistencias 100%. Brand guidelines visuales impactantes transmitiendo identidad clara. Paleta de colores accesible para todos los usuarios incluyendo daltonismo. Tipografía escalable desde mobile hasta 4K sin sacrificar legibilidad.`
            },
            {
                label: '🔍 Investigación UX',
                text: `Conducí 20+ entrevistas profundas descubriendo necesidades reales de usuarios. Testing con usuarios reales identificando fricciones antes del desarrollo. Heatmaps y session recordings revelando comportamientos no dichos. Personas de usuario basadas en datos guiando decisiones de diseño.`
            },
            {
                label: '📐 Wireframing',
                text: `Wireframes low-fidelity rápidos validando conceptos antes de invertir. High-fidelity detallado con especificaciones pixel-perfect para desarrolladores. User flows complejos mapeando toda la experiencia. Information architecture lógica facilitando navegación intuitiva.`
            },
            {
                label: '🎬 Prototipado',
                text: `Prototipos interactivos en Figma indistinguibles de producto final. Motion design cautivador con transiciones que delitan sin distraer. Micro-interacciones que comunican estado y retroalimentación clara. Iteraciones rápidas basadas en feedback real.`
            },
            {
                label: '♿ Accesibilidad',
                text: `WCAG 2.1 AA compliance garantizando acceso para todos. Testing con screen readers asegurando navegación perfect para ciegos. Contraste de colores optimizado para visibilidad excepcional. Keyboard navigation fluida sin necesidad de ratón. Interfaces incluibles para 100% de usuarios.`
            },
            {
                label: '📱 Diseño Responsivo',
                text: `Mobile-first ensuring excelencia en pantallas pequeñas. Breakpoints inteligentes: 320px, 768px, 1024px, 1440px cubriendo todo espectro. Gestos táctiles optimizados para usabilidad perfecta en smartphones. Experiencia consistente en desktop, tablet y mobile.`
            },
            {
                label: '📊 Impacto Medible',
                text: `Redesign que aumentó conversión 35% impactando ingresos directamente. Bounce rate reducido en 28% manteniendo usuarios enganchados. Tiempo en página aumentó 45% demostrando engagement genuino. NPS score escaló de 42 a 78 alcanzando satisfacción excepcional.`
            },
            {
                label: '🛠️ Herramientas',
                text: `Figma expert dominando cada feature para máxima eficiencia. Suite Adobe Creative completa (Photoshop, Illustrator, XD) para versatilidad. Análisis profundo con Hotjar, Mixpanel revelando comportamientos ocultos. Prototipos interactivos en Marvel convenciendo stakeholders.`
            },
            {
                label: '👥 Colaboración',
                text: `Partnerships fluidos con 15+ developers traduciendo diseños a código perfecto. Design reviews periódicos elevando estándar visual. Handoff meticulous de componentes con especificaciones detalladas. Pair designing sessions co-creando soluciones innovadoras.`
            },
            {
                label: '🎯 Branding',
                text: `Identidad visual impactante diferenciando marca en mercado saturado. Logo adaptable funcionando desde favicon hasta billboard. Brand voice guidelines coherentes en toda comunicación. Colateral marketing (presentaciones, banners) transmitiendo mensaje claro.`
            }
        ];
    }
    
    // ============================================
    // PRODUCT MANAGERS / JEFES DE PRODUCTO
    // ============================================
    if (position.includes('product') || position.includes('pm') || position.includes('jefe de producto') || position.includes('product manager') || position.includes('gestor de producto')) {
        return [
            {
                label: '🗺️ Roadmap',
                text: `Definí visión de producto clara para 12 meses alineando equipo detrás de objetivo común. Priorizacióne data-driven de 25+ features usando RICE scoring eliminando subjetividad. Roadmap comunicada transparentemente a organización generando alineamiento. Quarterly OKRs ambiciosos pero alcanzables dirigiendo esfuerzo.`
            },
            {
                label: '📊 Estrategia',
                text: `Go-to-market strategy ganadora para 3 launches principales. Market research profundo descubriendo white spaces no explorados. Competitive analysis posicionando producto diferenciadamente. Unique value proposition clara convenciendo clientes de elegir solución.`
            },
            {
                label: '👥 Liderazgo',
                text: `Lideré equipos multidisciplinarios de 12+ personas (developers, designers, QA) hacia objetivos comunes. Mentoring individual transformando talento potencial en especialistas. Retención de 95% demostrando liderazgo que inspira. Promoted 2 miembros reconociendo y acelerando talento.`
            },
            {
                label: '💼 Stakeholders',
                text: `Gestión experta de 20+ stakeholders con intereses conflictivos alineándolos. Presentaciones ejecutivas claras traduciendo estrategia a impacto medible. Customer feedback loops cerrando loop entre usuario y producto. Budget allocation inteligente maximizando ROI cada peso invertido.`
            },
            {
                label: '📈 Métricas',
                text: `OKRs transformadores: MAU creció 150%, Retención mejoró 35%, NPS escaló 25 puntos. DAU multiplicado 4.5x de 10k a 45k usuarios activos. Churn reducido de 8% a 3% mediante improvements sistemáticos. LTV aumentado 180% optimizando permanencia.`
            },
            {
                label: '🎯 Discovery',
                text: `20+ customer interviews mensuales buceando profundo en frustrations. Cohort analysis revelando patrones de uso y abandono. Behavioral segmentation identificando high-value user personas. Feature adoption tracking midiendo impacto real en producción.`
            },
            {
                label: '🚀 Lanzamientos',
                text: `5 features lanzadas a producción con zero critical bugs. Beta testing con 200+ power users proporcionando feedback invaluable. Launch marketing plan creando buzz y anticipación. Monitoreo obsesivo post-launch garantizando éxito sostenido.`
            },
            {
                label: '🔢 Data-Driven',
                text: `SQL avanzado para análisis quirúrgicos de datos. Dashboards custom en Tableau revelando insights ocultos. A/B testing disciplinado de 30+ experimentos reduciendo suposiciones. Statistical rigor validando decisions antes de escalar.`
            },
            {
                label: '💰 Monetización',
                text: `Pricing strategy optimizada capturando máximo value sin friccionar. Freemium conversion mejorada con incentivos inteligentes. LTV:CAC ratio alcanzado 3.5:1 asegurando unit economics sano. ARR escalado de $1M a $5M demostrando crecimiento exponencial.`
            },
            {
                label: '📢 Comunicación',
                text: `Quarterly business reviews estructurados demostrando progress transparente. Executive summaries cristalinas traducing complexity a clarity. Product updates communication creando anticipation genuina. Transparency radical sobre trade-offs construyendo confianza.`
            }
        ];
    }
    
    // ============================================
    // VENDEDORES / BUSINESS DEVELOPMENT
    // ============================================
    if (position.includes('vendedor') || position.includes('sales') || position.includes('account executive') || position.includes('business development') || position.includes('comercial')) {
        return [
            {
                label: '💰 Ventas Directas',
                text: `$2.5M cerrados en ventas demostrando capacidad de cierre exceptional. 150+ deals procesados con metodología sistemática y repeatable. Win rate de 38% por encima del promedio industria. Ciclo de ventas acelerado de 90 a 45 días mejorando predictabilidad.`
            },
            {
                label: '🎯 Prospecting',
                text: `500+ outreaches mensuales con respuesta rate de 15% superior al benchmark. 40% de prospects convertidos a meeting generando pipeline robusto. LinkedIn outreach strategy escalando presencia digital. Cold calling expertise rompiendo barreras iniciales.`
            },
            {
                label: '🤝 Negociación',
                text: `Contratos enterprise de $100k+ cerrados mediante negotiación win-win. Contract terms optimizados protegiendo márgenes. Vendor negotiations estratégicas reduciendo costos operacionales. Multi-threading profundo construyendo alianzas internas en accounts.`
            },
            {
                label: '👥 Account Management',
                text: `25+ cuentas enterprise gestionadas con relaciones sólidas. Expansion revenue de $500k generado en base existente. Renewals 100% demostrando customer satisfaction excepcional. Net retention de 120% evidenciando stickiness de producto. Onboarding perfecto de nuevos clientes.`
            },
            {
                label: '📊 Pipeline',
                text: `$5M en pipeline de oportunidades calificadas listo para cierre. 150 leads qualified con potencial real de conversión. Average deal size de $75k permitiendo forecast predecible. Sales cycle tracking religioso minimizando sorpresas.`
            },
            {
                label: '🛠️ Conocimiento',
                text: `Dominio exhaustivo de features traduciendo a beneficios reales. Use cases mapeados para cada vertical de industria. Technical evaluations convincentes eliminando objeciones. Demo expertise que deja prospects queriendo más.`
            },
            {
                label: '😊 Éxito del Cliente',
                text: `NPS score de 72 demostrando advocacy genuino de clientes. CSAT de 95% reflejando satisfacción excepcional. Churn de 0% en cartera propia manteniendo clientes por años. Testimonials espontáneos amplificando credibilidad.`
            },
            {
                label: '📢 Alineamiento',
                text: `Feedback constante a marketing mejorando targeting de campaigns. Lead quality insights elevando eficiencia de generación. Co-marketing initiatives multiplicando reach. Content collaboration relevante para sales process.`
            },
            {
                label: '🏆 Liderazgo',
                text: `Top 1% de vendedores en organización consistentemente. President's Club member ganador de reconocimiento. Training y mentorship de 3 reps nuevos elevando equipo. Promoted a Senior Role por track record.`
            },
            {
                label: '📱 CRM Master',
                text: `Salesforce experto maximizando cada feature de plataforma. 100% pipeline visibility en tiempo real. Forecast accuracy asombrosa de 95%+. Custom workflows automating tareas repetitivas. Reports inteligentes que revelan oportunidades.`
            }
        ];
    }
    
    // ============================================
    // MARKETING
    // ============================================
    if (position.includes('marketing') || position.includes('marketer') || position.includes('growth') || position.includes('especialista en marketing')) {
        return [
            {
                label: '📈 Growth',
                text: `CAC reducido 40% optimizando customer acquisition funnel. LTV mejorado a $8k mediante retention strategies. Crecimiento MoM de 25% compuesto mes a mes. Viral coefficient de 1.3 activando crecimiento exponencial. Referral program generando 5x ROI.`
            },
            {
                label: '📝 Content',
                text: `100+ artículos publicados posicionando como thought leader. SEO strategy generando 50k visitantes orgánicos mensuales. 20+ case studies demostrando ROI real. Webinar series atrayendo 2k+ asistentes calificados. Newsletter de 15k suscriptores altamente engaged.`
            },
            {
                label: '🌐 Digital',
                text: `Google Ads strategy generando 3.5x ROAS en $100k gastados. Facebook Ads optimizadas a CTR de 2.1% y CPC de $0.85. LinkedIn campaigns dirigidas a decisores de C-suite. Retargeting inteligente recuperando 30% usuarios perdidos.`
            },
            {
                label: '🎨 Branding',
                text: `Brand awareness campaign alcanzando 60% aided awareness. Positioning statement diferenciador en mercado saturado. Visual identity system coherente en todos touchpoints. Brand guidelines asegurando consistencia perfecta.`
            },
            {
                label: '✉️ Email',
                text: `Email campaigns con 35% open rate superando industria. CTR de 4.2% demostrando copy relevante. Segmentación avanzada de 10 cohorts personalizando mensajes. Drip campaigns automáticos nutriendo leads sin fricción. 80k suscriptores altamente engaged.`
            },
            {
                label: '📱 Social',
                text: `Twitter community de 50k followers con 2% engagement rate. LinkedIn strategy construyendo B2B community de 15k decision makers. Instagram strategy escalando a 30k followers con content calendar planificado. TikTok viral content logrando 100k+ views.`
            },
            {
                label: '📊 Analytics',
                text: `Google Analytics experto revelando customer journey completo. Dashboards custom en Tableau mostrando KPIs en tiempo real. Mixpanel for product analytics rastreando comportamiento usuario. Attribution modeling multi-touch asignando crédito correctamente.`
            },
            {
                label: '🤝 Partnership',
                text: `5+ partnerships estratégicas escalando reach exponencialmente. Co-marketing campaigns leveraging audiencias complementarias. Channel partnerships distribuyendo through múltiples canales. Affiliate program crecimiento acelerado. Ecosystem building creando network effects.`
            },
            {
                label: '🎪 Eventos',
                text: `3 eventos live organizados con 500+ asistentes cada uno generando leads. Virtual conferences alcanzando audiencia global. Sponsorship strategy ROI maximizado. Event marketing pre/durante/post ejecutado perfectamente.`
            },
            {
                label: '🚀 Lanzamientos',
                text: `2 product launches ejecutados con precision milimétrica. Pre-launch buzz generando 5k waitlist anticipado. Launch day strategy creando spike de awareness. Day 1 revenue de $200k superando targets. Media coverage en 15+ publications amplificando reach.`
            }
        ];
    }
    
    // ============================================
    // CIENTÍFICOS DE DATOS / ANALISTAS
    // ============================================
    if (position.includes('data scientist') || position.includes('data analyst') || position.includes('analista') || position.includes('científico de datos')) {
        return [
            {
                label: '🤖 Machine Learning',
                text: `Modelos ML con accuracy de 92% prediciendo comportamientos reales. Python expertise (scikit-learn, TensorFlow) resolviendo problemas complejos. Recommendation engine aumentando engagement en 40%. NLP avanzado procesando lenguaje natural con precisión.`
            },
            {
                label: '📊 Analytics',
                text: `SQL avanzado extrayendo insights de datos complejos. BigQuery arquitectura escalando a billones de registros. Data warehouse design optimizado para queries rápidas. ETL pipelines automatizando data flow. 200+ reports automatizados ahorrando 100+ horas.`
            },
            {
                label: '📈 Estadística',
                text: `A/B testing expertise con rigor estadístico validando hipótesis. Statistical significance calculations eliminando falsos positivos. Regression analysis identificando relaciones causales. Time series forecasting prediciendo tendencias futuras.`
            },
            {
                label: '📉 Visualización',
                text: `Dashboards interactivos en Tableau contando historias con datos. Python visualization mastery (matplotlib, seaborn, plotly) creando insights visuales. Data storytelling convenciendo stakeholders a actuar. Executive dashboards comunicando KPIs críticos.`
            },
            {
                label: '💼 Impacto',
                text: `Insights que generaron $500k en revenue directamente. Modelo predictivo de churn reduciendo abandono en 15%. Optimization projects ahorrando $200k en costos operacionales. Business impact monetizado demostrando valor concreto.`
            },
            {
                label: '🐍 Coding',
                text: `Python experto dominando pandas, numpy, scikit-learn. SQL avanzado escribiendo queries optimizadas. R programming para análisis estadístico. Jupyter notebooks documentando análisis completamente. Git/GitHub colaborando fluidamente.`
            },
            {
                label: '🎯 Presentación',
                text: `Presentaciones mensuales a C-suite comunicando insights complejos claramente. Executive summaries cristalinas traduciendo data a decisiones. Data storytelling convincente moviendo stakeholders. Visualizaciones impactantes que entiende cualquiera.`
            },
            {
                label: '👥 Colaboración',
                text: `Cross-functional projects con product, engineering y business. Stakeholder management consultando sobre data strategy. Requirements gathering asegurando relevancia. Feedback loops iterativos mejorando continuamente.`
            }
        ];
    }
    
    // ============================================
    // FALLBACK GENÉRICO (para CUALQUIER rol)
    // Si no matchea con ninguno de arriba, devuelve estas sugerencias universales
    // ============================================
    return [
        {
            label: '🎯 Responsabilidades',
            text: `Ejecuté [Responsabilidad principal] con excelencia excepcional. Dirigí [N] proyectos estratégicos impactando resultados. Colaboré sinérgicamente con [N] departamentos alineando esfuerzos. Reporté a [Cargo] elevando decisiones críticas.`
        },
        {
            label: '⭐ Logros ',
            text: `Superé objetivos en [X]% demostrando compromiso inquebrantable. Implementé [Iniciativa] que transformó [Área] completamente. Reconocimiento especial: [Premio/Ascenso/Éxito notable]. Impacto duradero beneficiando organización.`
        },
        {
            label: '📊 Métricas',
            text: `Aumenté [Métrica clave] en [X]% generando impacto medible. Reducí [Ineficiencia] en [X]% mejorando operaciones. Escalé [KPI] de [Valor inicial] a [Valor final] superando targets. Ahorro total: [Dinero/Tiempo] impactando P&L directamente.`
        },
        {
            label: '👥 Trabajo en Equipo',
            text: `Colaboré estrechamente con [N] personas en [Proyecto] logrando excellence. Capacité [N] colegas elevando habilidades del equipo. Lideré iniciativa de [Descripción] inspirando participación. Mentoría constructiva desarrollando talento emergente.`
        },
        {
            label: '💪 Habilidades',
            text: `Desarrollé expertise profundo en [Herramienta/Proceso] dominándolo completamente. Certificación obtenida en [Estándar/Skill] validando conocimiento. Dominio demostrando en [Software/Metodología] con aplicación práctica. Experiencia adquirida traduciendo a valor inmediato.`
        },
        {
            label: '🚀 Transformación',
            text: `Iniciativa que transformó [Proceso] mejorando efficiency en [X]%. Automatización inteligente ahorrando [X horas/mes] liberando capacidad. Escalabilidad: sistema creció de [Escala A] a [Escala B] soportando crecimiento. Legacy duradero beneficiando años.`
        }
    ];
};

// Función helper para obtener solo las sugerencias (sin la categoría)
export const getSimpleSuggestions = (experience: Experience) => {
    return getDescriptionSuggestions(experience).map(s => ({
        label: s.label,
        text: s.text
    }));
};