CREATE TABLE sr_users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    public_id VARCHAR(255) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    role ENUM('student', 'recruiter', 'admin') NOT NULL,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

CREATE TABLE sr_student_profiles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    public_id VARCHAR(255) NOT NULL UNIQUE,
    user_id BIGINT UNSIGNED NOT NULL,
    headline VARCHAR(255) NULL,
    location VARCHAR(255) NULL,
    education VARCHAR(255) NULL,
    experience_years TINYINT UNSIGNED NOT NULL DEFAULT 0,
    skills JSON NULL,
    links JSON NULL,
    cv_text LONGTEXT NULL,
    cv_metadata JSON NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT sr_student_profiles_user_id_foreign FOREIGN KEY (user_id) REFERENCES sr_users(id) ON DELETE CASCADE
);

CREATE TABLE sr_recruiter_profiles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    public_id VARCHAR(255) NOT NULL UNIQUE,
    user_id BIGINT UNSIGNED NOT NULL,
    company_name VARCHAR(255) NOT NULL,
    position VARCHAR(255) NULL,
    website VARCHAR(255) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT sr_recruiter_profiles_user_id_foreign FOREIGN KEY (user_id) REFERENCES sr_users(id) ON DELETE CASCADE
);

CREATE TABLE sr_cv_documents (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_profile_id BIGINT UNSIGNED NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    stored_path VARCHAR(255) NOT NULL,
    mime_type VARCHAR(255) NULL,
    size INT UNSIGNED NOT NULL DEFAULT 0,
    parser_result JSON NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT sr_cv_documents_student_profile_id_foreign FOREIGN KEY (student_profile_id) REFERENCES sr_student_profiles(id) ON DELETE CASCADE
);

CREATE TABLE sr_offers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    public_id VARCHAR(255) NOT NULL UNIQUE,
    recruiter_profile_id BIGINT UNSIGNED NULL,
    title VARCHAR(255) NOT NULL,
    company VARCHAR(255) NOT NULL,
    location VARCHAR(255) NULL,
    type VARCHAR(255) NOT NULL DEFAULT 'Stage',
    description LONGTEXT NOT NULL,
    required_skills JSON NULL,
    status ENUM('draft', 'published', 'closed') NOT NULL DEFAULT 'published',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX sr_offers_status_created_at_index (status, created_at),
    CONSTRAINT sr_offers_recruiter_profile_id_foreign FOREIGN KEY (recruiter_profile_id) REFERENCES sr_recruiter_profiles(id) ON DELETE SET NULL
);

CREATE TABLE sr_applications (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    public_id VARCHAR(255) NOT NULL UNIQUE,
    offer_id BIGINT UNSIGNED NOT NULL,
    student_profile_id BIGINT UNSIGNED NOT NULL,
    status ENUM('submitted', 'shortlisted', 'rejected', 'interview') NOT NULL DEFAULT 'submitted',
    applied_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    UNIQUE KEY sr_applications_offer_student_unique (offer_id, student_profile_id),
    CONSTRAINT sr_applications_offer_id_foreign FOREIGN KEY (offer_id) REFERENCES sr_offers(id) ON DELETE CASCADE,
    CONSTRAINT sr_applications_student_profile_id_foreign FOREIGN KEY (student_profile_id) REFERENCES sr_student_profiles(id) ON DELETE CASCADE
);

CREATE TABLE sr_match_scores (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    application_id BIGINT UNSIGNED NOT NULL,
    score TINYINT UNSIGNED NOT NULL,
    text_similarity TINYINT UNSIGNED NOT NULL DEFAULT 0,
    semantic_coverage TINYINT UNSIGNED NOT NULL DEFAULT 0,
    matched_skills JSON NULL,
    missing_skills JSON NULL,
    raw_response JSON NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX sr_match_scores_score_index (score),
    CONSTRAINT sr_match_scores_application_id_foreign FOREIGN KEY (application_id) REFERENCES sr_applications(id) ON DELETE CASCADE
);
