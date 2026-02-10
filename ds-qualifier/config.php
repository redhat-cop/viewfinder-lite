<?php
/**
 * Digital Sovereignty Readiness Assessment - Questions Configuration
 *
 * This file contains the qualifying questions for the readiness assessment
 * Designed for quick 10-15 minute evaluations of digital sovereignty readiness
 */

return [
    'Data Sovereignty' => [
        'domain_key' => 'Domain-1',
        'description' => 'Data control, residency, and encryption sovereignty',
        'questions' => [
            [
                'id' => 'ds1',
                'text' => 'Does your organization currently comply with all data residency requirements or regulations relevant to your country/region/vertical?',
                'weight' => 1,
                'tooltip' => 'Examples: GDPR (EU), PIPEDA (Canada), LGPD (Brazil), industry regulations requiring data to stay within specific jurisdictions.'
            ],
            [
                'id' => 'ds2',
                'text' => 'Do you control and manage your encryption keys exclusively (not shared with cloud providers)?',
                'weight' => 1,
                'tooltip' => 'BYOK (Bring Your Own Key) or HYOK (Hold Your Own Key) ensures only you can decrypt data, not the cloud provider.'
            ],
            [
                'id' => 'ds3',
                'text' => 'Can you prevent sensitive data from crossing specific geographic borders?',
                'weight' => 1,
                'tooltip' => 'Critical for compliance with data localization laws. Requires technical controls to ensure data stays within approved regions/countries.'
            ]
        ]
    ],

    'Technical Sovereignty' => [
        'domain_key' => 'Domain-2',
        'description' => 'Technology independence and platform portability',
        'questions' => [
            [
                'id' => 'ts1',
                'text' => 'Can you mitigate vendor lock-in risks with your current technology stack?',
                'weight' => 1,
                'tooltip' => 'Vendor lock-in occurs when proprietary technologies make it difficult or expensive to switch providers. Open source and standards-based platforms reduce this risk.'
            ],
            [
                'id' => 'ts2',
                'text' => 'Do you prioritize open standards over proprietary APIs in your platforms?',
                'weight' => 1,
                'tooltip' => 'Open standards (Kubernetes, OCI containers, POSIX) ensure portability and interoperability. Proprietary APIs create dependencies on specific vendors.'
            ],
            [
                'id' => 'ts3',
                'text' => 'Can you migrate critical applications to different cloud platforms if needed?',
                'weight' => 1,
                'tooltip' => 'True cloud portability means workloads can move between providers (AWS, Azure, on-prem) without major rewrites.'
            ]
        ]
    ],

    'Operational Sovereignty' => [
        'domain_key' => 'Domain-3',
        'description' => 'Operational independence and resilience',
        'questions' => [
            [
                'id' => 'os1',
                'text' => 'Can you continue operating critical systems if external cloud services become unavailable?',
                'weight' => 1,
                'tooltip' => 'Operational resilience means critical systems can run independently if cloud providers have outages or service disruptions.'
            ],
            [
                'id' => 'os2',
                'text' => 'Do you have in-house technical expertise to manage sovereign infrastructure?',
                'weight' => 1,
                'tooltip' => 'Managing sovereign systems requires specialized skills in security, compliance, and infrastructure management.'
            ],
            [
                'id' => 'os3',
                'text' => 'Do you have disaster recovery plans that account for geopolitical scenarios?',
                'weight' => 1,
                'tooltip' => 'Geopolitical risks include sanctions, trade restrictions, and data access laws (CLOUD Act, etc.). DR plans should address scenarios where international providers may be restricted.'
            ]
        ]
    ],

    'Assurance Sovereignty' => [
        'domain_key' => 'Domain-4',
        'description' => 'Security, compliance, and audit control',
        'questions' => [
            [
                'id' => 'as1',
                'text' => 'Do you have contractual rights to audit your vendors\' security practices and controls?',
                'weight' => 1,
                'tooltip' => 'Audit rights allow independent verification of vendor security claims. Critical for regulated industries and government organizations.'
            ],
            [
                'id' => 'as2',
                'text' => 'Do you control where your security logs and audit trails are stored?',
                'weight' => 1,
                'tooltip' => 'Security logs contain sensitive information and must meet retention and location requirements. Storing logs with the same vendor creates a single point of failure.'
            ],
            [
                'id' => 'as3',
                'text' => 'Is your organization certified against your country\'s national security standards (e.g., NIS2, SecNumCloud)?',
                'weight' => 1,
                'tooltip' => 'National certifications (NIS2 in EU, SecNumCloud in France, FedRAMP in US) prove compliance with sovereign security requirements.'
            ]
        ]
    ],

    'Open Source' => [
        'domain_key' => 'Domain-5',
        'description' => 'Open source strategy and independence',
        'questions' => [
            [
                'id' => 'oss1',
                'text' => 'Do you have a formal policy favoring open-source software over proprietary alternatives?',
                'weight' => 1,
                'tooltip' => 'Many governments and regulated organizations mandate open source for transparency and sovereignty. Formal policies drive procurement decisions.'
            ],
            [
                'id' => 'oss2',
                'text' => 'Can you fork and independently maintain critical open-source dependencies if needed?',
                'weight' => 1,
                'tooltip' => 'True software sovereignty means the ability to take ownership if upstream projects change direction or become unavailable.'
            ],
            [
                'id' => 'oss3',
                'text' => 'Do you actively contribute to strategic open-source projects important to your operations?',
                'weight' => 1,
                'tooltip' => 'Contributing to OSS communities ensures influence over project direction and builds internal expertise.'
            ]
        ]
    ],

    'Executive Oversight' => [
        'domain_key' => 'Domain-6',
        'description' => 'Strategic governance and leadership commitment',
        'questions' => [
            [
                'id' => 'eo1',
                'text' => 'Do you have an executive sponsor or steering committee for digital sovereignty initiatives?',
                'weight' => 1,
                'tooltip' => 'Executive sponsorship ensures funding, priority, and cross-organizational alignment for digital sovereignty initiatives.'
            ],
            [
                'id' => 'eo2',
                'text' => 'Is digital sovereignty explicitly part of your corporate or IT strategy?',
                'weight' => 1,
                'tooltip' => 'Strategic commitment to digital sovereignty drives technology choices, vendor selection, and architecture decisions.'
            ],
            [
                'id' => 'eo3',
                'text' => 'Do you have a dedicated budget allocated for sovereignty initiatives and compliance?',
                'weight' => 1,
                'tooltip' => 'Budget allocation indicates seriousness and enables execution of digital sovereignty programs.'
            ]
        ]
    ],

    'Managed Services' => [
        'domain_key' => 'Domain-7',
        'description' => 'Cloud service control and provider independence',
        'questions' => [
            [
                'id' => 'ms1',
                'text' => 'Can you restrict cloud deployments to specific regions or certified data centers?',
                'weight' => 1,
                'tooltip' => 'Regional restrictions ensure compliance with data residency laws and reduce geopolitical risk.'
            ],
            [
                'id' => 'ms2',
                'text' => 'Do you control and monitor your cloud provider\'s administrative access to your systems?',
                'weight' => 1,
                'tooltip' => 'Privileged access management ensures only authorized personnel can access systems.'
            ],
            [
                'id' => 'ms3',
                'text' => 'Have you tested or validated the ability to migrate workloads to different cloud providers?',
                'weight' => 1,
                'tooltip' => 'Regular migration testing proves portability isn\'t just theoretical.'
            ]
        ]
    ]
];
