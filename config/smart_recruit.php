<?php

return [
    'ai_service_url' => env('AI_SERVICE_URL', 'http://127.0.0.1:8010'),

    'default_password' => env('SMART_RECRUIT_DEFAULT_PASSWORD', 'smart-recruit-demo'),

    // Libellés partagés par toutes les vues (évite de les redéclarer dans chaque Blade).
    'application_status_labels' => [
        'submitted' => 'Reçue',
        'shortlisted' => 'Présélection',
        'interview' => 'Acceptée',
        'rejected' => 'Refusée',
    ],

    'offer_status_labels' => [
        'published' => 'Publiée',
        'draft' => 'Brouillon',
        'closed' => 'Fermée',
    ],

    'role_labels' => [
        'admin' => 'Administrateur',
        'recruiter' => 'Recruteur',
        'student' => 'Candidat',
    ],

    'skill_aliases' => [
        'java' => ['java', 'j2ee', 'jee', 'java enterprise', 'spring', 'spring boot'],
        'php' => ['php', 'laravel', 'symfony'],
        'python' => ['python', 'fastapi', 'flask', 'django'],
        'javascript' => ['javascript', 'js', 'ecmascript'],
        'typescript' => ['typescript', 'ts'],
        'react' => ['react', 'reactjs', 'react.js', 'front-end react', 'frontend react'],
        'vue' => ['vue', 'vuejs', 'vue.js'],
        'angular' => ['angular', 'angularjs'],
        'frontend' => ['frontend', 'front end', 'front-end', 'ui', 'interface utilisateur'],
        'backend' => ['backend', 'back end', 'back-end', 'api rest', 'rest api'],
        'sql' => ['sql', 'mysql', 'postgresql', 'postgres', 'oracle', 'mariadb'],
        'nosql' => ['mongodb', 'nosql', 'redis', 'elasticsearch'],
        'devops' => ['devops', 'docker', 'ci cd', 'ci/cd', 'gitlab ci', 'github actions'],
        'cloud' => ['cloud', 'aws', 'azure', 'gcp'],
        'machine learning' => ['machine learning', 'ml', 'apprentissage automatique', 'scikit-learn', 'sklearn'],
        'nlp' => ['nlp', 'traitement du langage naturel', 'spacy', 'bert', 'tf-idf', 'tfidf'],
        'data analysis' => ['data analysis', 'analyse de données', 'pandas', 'numpy', 'power bi'],
        'mobile' => ['mobile', 'android', 'ios', 'flutter', 'react native'],
        'security' => ['cybersécurité', 'cybersecurity', 'owasp', 'sécurité'],
        'git' => ['git', 'github', 'gitlab'],
        'agile' => ['agile', 'scrum', 'kanban'],
    ],

    'skill_taxonomy' => [
        'java' => 'backend',
        'php' => 'backend',
        'python' => 'backend',
        'javascript' => 'frontend',
        'typescript' => 'frontend',
        'react' => 'frontend',
        'vue' => 'frontend',
        'angular' => 'frontend',
        'frontend' => 'frontend',
        'backend' => 'backend',
        'sql' => 'data',
        'nosql' => 'data',
        'devops' => 'infrastructure',
        'cloud' => 'infrastructure',
        'machine learning' => 'ai',
        'nlp' => 'ai',
        'data analysis' => 'data',
        'mobile' => 'mobile',
        'security' => 'security',
        'git' => 'tools',
        'agile' => 'methods',
    ],
];
