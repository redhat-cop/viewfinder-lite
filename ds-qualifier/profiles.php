<?php
/**
 * Digital Sovereignty Readiness Assessment - Weighting Profiles
 *
 * Defines industry/context-specific weighting profiles for domain scoring
 * Weights: 1.0 = standard, 1.5 = higher priority, 2.0 = critical
 */

return [
    'balanced' => [
        'name' => 'Balanced',
        'description' => 'Equal weighting across all domains - suitable for general assessments. This profile provides a comprehensive baseline evaluation without emphasizing any particular domain, making it ideal for organizations beginning their digital sovereignty journey or those without specific regulatory constraints.',
        'icon' => 'fa-balance-scale',
        'weights' => [
            'Data Sovereignty' => 1.0,
            'Technical Sovereignty' => 1.0,
            'Operational Sovereignty' => 1.0,
            'Assurance Sovereignty' => 1.0,
            'Open Source' => 1.0,
            'Executive Oversight' => 1.0,
            'Managed Services' => 1.0
        ]
    ],

    'financial' => [
        'name' => 'Financial Services',
        'description' => 'Emphasizes data protection, compliance, and audit controls for banking and finance. Financial institutions face stringent regulatory requirements including PCI DSS, anti-money laundering laws, and data residency mandates that require demonstrable control over customer data and transaction records. Regulators increasingly demand independent audit rights and the ability to verify that financial data remains within approved jurisdictions.',
        'icon' => 'fa-building-columns',
        'weights' => [
            'Data Sovereignty' => 2.0,      // Critical: PCI DSS, data residency
            'Technical Sovereignty' => 1.0,
            'Operational Sovereignty' => 1.5, // Important: Business continuity
            'Assurance Sovereignty' => 2.0,   // Critical: Audit requirements
            'Open Source' => 1.0,
            'Executive Oversight' => 1.5,     // Important: Governance
            'Managed Services' => 1.5         // Important: Third-party risk
        ]
    ],

    'healthcare' => [
        'name' => 'Healthcare',
        'description' => 'Focuses on patient data protection, operational resilience, and regulatory compliance. Healthcare organizations must safeguard sensitive patient information under HIPAA, GDPR, and local health data regulations while ensuring 24/7 availability of life-critical systems. The sector faces unique challenges balancing data sovereignty requirements with the need for cross-border medical research collaboration and emergency care coordination.',
        'icon' => 'fa-heart-pulse',
        'weights' => [
            'Data Sovereignty' => 2.0,        // Critical: HIPAA, patient data
            'Technical Sovereignty' => 1.0,
            'Operational Sovereignty' => 2.0, // Critical: Patient safety
            'Assurance Sovereignty' => 1.5,   // Important: Compliance
            'Open Source' => 1.0,
            'Executive Oversight' => 1.5,
            'Managed Services' => 1.5
        ]
    ],

    'government' => [
        'name' => 'Government & Public Sector',
        'description' => 'Comprehensive sovereignty across all domains for public sector organizations. Government agencies handle sensitive citizen data and critical national infrastructure, making digital sovereignty essential for national security and public trust. Regulations like NIS2, FedRAMP, and national cybersecurity frameworks mandate strict controls over data location, vendor access, and the ability to maintain operations during geopolitical disruptions or trade restrictions.',
        'icon' => 'fa-landmark',
        'weights' => [
            'Data Sovereignty' => 2.0,        // Critical: Citizen data
            'Technical Sovereignty' => 1.5,   // Important: Independence
            'Operational Sovereignty' => 1.5, // Important: Continuity
            'Assurance Sovereignty' => 2.0,   // Critical: National security
            'Open Source' => 1.5,             // Important: Transparency
            'Executive Oversight' => 2.0,     // Critical: Accountability
            'Managed Services' => 1.5         // Important: Control
        ]
    ],

    'technology' => [
        'name' => 'Technology & SaaS',
        'description' => 'Prioritizes technical independence, portability, and open source strategy. Technology companies must avoid vendor lock-in to maintain competitive agility and rapidly adapt to market changes. Building on open standards and contributing to open source communities enables faster innovation, reduces proprietary dependencies, and provides the flexibility to deploy across multiple cloud providers or migrate to new platforms as business needs evolve.',
        'icon' => 'fa-laptop-code',
        'weights' => [
            'Data Sovereignty' => 1.5,
            'Technical Sovereignty' => 2.0,   // Critical: Vendor lock-in
            'Operational Sovereignty' => 1.5, // Important: Scalability
            'Assurance Sovereignty' => 1.0,
            'Open Source' => 2.0,             // Critical: Innovation
            'Executive Oversight' => 1.0,
            'Managed Services' => 1.5         // Important: Multi-cloud
        ]
    ],

    'manufacturing' => [
        'name' => 'Manufacturing & Industrial',
        'description' => 'Emphasizes operational resilience, service continuity, and supply chain control. Manufacturing organizations depend on continuous production operations and just-in-time supply chains that cannot tolerate extended downtime. Protecting intellectual property in design and manufacturing processes is critical, while OT/IT convergence creates new sovereignty challenges as industrial control systems become increasingly cloud-connected and dependent on external service providers.',
        'icon' => 'fa-industry',
        'weights' => [
            'Data Sovereignty' => 1.5,        // Important: IP protection
            'Technical Sovereignty' => 1.0,
            'Operational Sovereignty' => 2.0, // Critical: Production uptime
            'Assurance Sovereignty' => 1.5,   // Important: Quality systems
            'Open Source' => 1.0,
            'Executive Oversight' => 1.5,
            'Managed Services' => 2.0         // Critical: OT/IT integration
        ]
    ],

    'telecommunications' => [
        'name' => 'Telecommunications',
        'description' => 'Focuses on infrastructure sovereignty, operational independence, and regulatory compliance. Telecommunications providers are designated as critical infrastructure under regulations like NIS2 and face heightened scrutiny over network security and data handling. Governments increasingly require telcos to ensure subscriber data and network management remain under national control, while maintaining 24/7 service availability and protecting against foreign surveillance or interference.',
        'icon' => 'fa-tower-cell',
        'weights' => [
            'Data Sovereignty' => 2.0,        // Critical: Subscriber data
            'Technical Sovereignty' => 1.5,   // Important: Network independence
            'Operational Sovereignty' => 2.0, // Critical: Service availability
            'Assurance Sovereignty' => 2.0,   // Critical: NIS2, telecoms regulations
            'Open Source' => 1.0,
            'Executive Oversight' => 1.5,
            'Managed Services' => 1.5
        ]
    ],

    'energy' => [
        'name' => 'Energy & Utilities',
        'description' => 'Prioritizes critical infrastructure protection, operational resilience, and regulatory compliance. Energy and utility providers manage essential services where any disruption can have immediate public safety and economic consequences. As critical infrastructure operators under directives like NIS2 and NERC CIP, these organizations must demonstrate robust cybersecurity controls, protect SCADA and grid management systems from foreign interference, and maintain operational independence even during geopolitical crises.',
        'icon' => 'fa-bolt',
        'weights' => [
            'Data Sovereignty' => 1.5,
            'Technical Sovereignty' => 1.5,
            'Operational Sovereignty' => 2.0, // Critical: Grid reliability
            'Assurance Sovereignty' => 2.0,   // Critical: Critical infrastructure
            'Open Source' => 1.0,
            'Executive Oversight' => 1.5,
            'Managed Services' => 1.5
        ]
    ],

    'custom' => [
        'name' => 'Custom',
        'description' => 'Set your own domain weightings to match your specific organizational priorities. Use the sliders below to adjust the importance of each domain based on your unique regulatory requirements, business model, risk profile, or strategic objectives. This allows you to create a tailored assessment that reflects your organization\'s specific digital sovereignty concerns and compliance obligations.',
        'icon' => 'fa-sliders',
        'weights' => [
            'Data Sovereignty' => 1.0,
            'Technical Sovereignty' => 1.0,
            'Operational Sovereignty' => 1.0,
            'Assurance Sovereignty' => 1.0,
            'Open Source' => 1.0,
            'Executive Oversight' => 1.0,
            'Managed Services' => 1.0
        ]
    ]
];
