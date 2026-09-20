-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : dim. 13 sep. 2026 à 17:20
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `recruit`
--

-- --------------------------------------------------------

--
-- Structure de la table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2026_05_27_000001_create_smart_recruit_tables', 1),
(2, '2026_09_11_000001_add_soft_deletes', 2),
(3, '2026_09_11_000002_increase_deleted_at_precision', 3);

-- --------------------------------------------------------

--
-- Structure de la table `sr_applications`
--

CREATE TABLE `sr_applications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `public_id` varchar(255) NOT NULL,
  `offer_id` bigint(20) UNSIGNED NOT NULL,
  `student_profile_id` bigint(20) UNSIGNED NOT NULL,
  `status` enum('submitted','shortlisted','rejected','interview') NOT NULL DEFAULT 'submitted',
  `applied_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp(6) NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `sr_applications`
--

INSERT INTO `sr_applications` (`id`, `public_id`, `offer_id`, `student_profile_id`, `status`, `applied_at`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'app_001', 1, 1, 'shortlisted', '2026-05-27 09:30:00', '2026-05-27 09:30:00', '2026-09-11 05:45:53', NULL),
(2, 'app_002', 1, 3, 'submitted', '2026-05-27 09:35:00', '2026-05-27 09:35:00', '2026-09-11 10:43:05', NULL),
(3, 'app_003', 2, 2, 'shortlisted', '2026-05-27 09:40:00', '2026-05-27 09:40:00', '2026-09-11 05:33:27', NULL),
(5, 'app_seed_001', 3, 4, 'shortlisted', '2026-05-28 11:00:00', '2026-05-28 11:00:00', '2026-09-11 05:33:14', NULL),
(6, 'app_seed_002', 3, 17, 'submitted', '2026-05-28 11:01:00', '2026-05-28 11:01:00', '2026-09-11 05:33:14', NULL),
(7, 'app_seed_003', 3, 5, 'shortlisted', '2026-05-28 11:02:00', '2026-05-28 11:02:00', '2026-09-11 05:33:14', NULL),
(8, 'app_seed_004', 3, 23, 'submitted', '2026-05-28 11:03:00', '2026-05-28 11:03:00', '2026-09-11 05:33:14', NULL),
(9, 'app_seed_005', 4, 5, 'shortlisted', '2026-05-28 11:04:00', '2026-05-28 11:04:00', '2026-09-11 05:33:14', NULL),
(10, 'app_seed_006', 4, 16, 'submitted', '2026-05-28 11:05:00', '2026-05-28 11:05:00', '2026-09-11 05:33:14', NULL),
(11, 'app_seed_007', 4, 15, 'submitted', '2026-05-28 11:06:00', '2026-05-28 11:06:00', '2026-09-11 05:33:14', NULL),
(12, 'app_seed_008', 4, 20, 'shortlisted', '2026-05-28 11:07:00', '2026-05-28 11:07:00', '2026-09-11 05:33:14', NULL),
(13, 'app_seed_009', 5, 6, 'shortlisted', '2026-05-28 11:08:00', '2026-05-28 11:08:00', '2026-09-11 05:33:14', NULL),
(14, 'app_seed_010', 5, 21, 'submitted', '2026-05-28 11:09:00', '2026-05-28 11:09:00', '2026-09-11 05:33:14', NULL),
(15, 'app_seed_011', 5, 3, 'submitted', '2026-05-28 11:10:00', '2026-05-28 11:10:00', '2026-09-11 10:43:05', NULL),
(16, 'app_seed_012', 5, 19, 'rejected', '2026-05-28 11:11:00', '2026-05-28 11:11:00', '2026-09-11 05:33:14', NULL),
(17, 'app_seed_013', 6, 7, 'shortlisted', '2026-05-28 11:12:00', '2026-05-28 11:12:00', '2026-09-11 05:33:14', NULL),
(18, 'app_seed_014', 6, 13, 'shortlisted', '2026-05-28 11:13:00', '2026-05-28 11:13:00', '2026-09-11 05:33:14', NULL),
(19, 'app_seed_015', 6, 14, 'submitted', '2026-05-28 11:14:00', '2026-05-28 11:14:00', '2026-09-11 05:33:14', NULL),
(20, 'app_seed_016', 6, 20, 'submitted', '2026-05-28 11:15:00', '2026-05-28 11:15:00', '2026-09-11 05:33:14', NULL),
(21, 'app_seed_017', 7, 8, 'shortlisted', '2026-05-28 11:16:00', '2026-05-28 11:16:00', '2026-09-11 10:43:04', NULL),
(22, 'app_seed_018', 7, 22, 'submitted', '2026-05-28 11:17:00', '2026-05-28 11:17:00', '2026-09-11 10:43:04', NULL),
(23, 'app_seed_019', 7, 12, 'submitted', '2026-05-28 11:18:00', '2026-05-28 11:18:00', '2026-09-11 10:43:04', NULL),
(24, 'app_seed_020', 7, 6, 'interview', '2026-05-28 11:19:00', '2026-05-28 11:19:00', '2026-09-11 10:43:04', NULL),
(25, 'app_seed_021', 8, 9, 'shortlisted', '2026-05-28 11:20:00', '2026-05-28 11:20:00', '2026-09-11 05:33:14', NULL),
(26, 'app_seed_022', 8, 17, 'submitted', '2026-05-28 11:21:00', '2026-05-28 11:21:00', '2026-09-11 05:33:14', NULL),
(27, 'app_seed_023', 8, 23, 'submitted', '2026-05-28 11:22:00', '2026-05-28 11:22:00', '2026-09-11 05:33:14', NULL),
(28, 'app_seed_024', 8, 19, 'rejected', '2026-05-28 11:23:00', '2026-05-28 11:23:00', '2026-09-11 05:33:14', NULL),
(29, 'app_seed_025', 9, 10, 'shortlisted', '2026-05-28 11:24:00', '2026-05-28 11:24:00', '2026-09-11 05:33:14', NULL),
(30, 'app_seed_026', 9, 15, 'submitted', '2026-05-28 11:25:00', '2026-05-28 11:25:00', '2026-09-11 05:33:14', NULL),
(31, 'app_seed_027', 9, 16, 'submitted', '2026-05-28 11:26:00', '2026-05-28 11:26:00', '2026-09-11 05:33:14', NULL),
(32, 'app_seed_028', 9, 1, 'shortlisted', '2026-05-28 11:27:00', '2026-05-28 11:27:00', '2026-09-11 05:33:14', NULL),
(33, 'app_seed_029', 10, 11, 'shortlisted', '2026-05-28 11:28:00', '2026-05-28 11:28:00', '2026-09-11 05:33:14', NULL),
(34, 'app_seed_030', 10, 18, 'submitted', '2026-05-28 11:29:00', '2026-05-28 11:29:00', '2026-09-11 05:33:14', NULL),
(35, 'app_seed_031', 10, 7, 'submitted', '2026-05-28 11:30:00', '2026-05-28 11:30:00', '2026-09-11 05:33:14', NULL),
(36, 'app_seed_032', 10, 14, 'rejected', '2026-05-28 11:31:00', '2026-05-28 11:31:00', '2026-09-11 05:33:14', NULL),
(37, 'app_seed_033', 11, 12, 'shortlisted', '2026-05-28 11:32:00', '2026-05-28 11:32:00', '2026-09-11 05:33:14', NULL),
(38, 'app_seed_034', 11, 8, 'submitted', '2026-05-28 11:33:00', '2026-05-28 11:33:00', '2026-09-11 05:33:14', NULL),
(39, 'app_seed_035', 11, 18, 'shortlisted', '2026-05-28 11:34:00', '2026-05-28 11:34:00', '2026-09-11 05:33:14', NULL),
(40, 'app_seed_036', 11, 22, 'submitted', '2026-05-28 11:35:00', '2026-05-28 11:35:00', '2026-09-11 05:33:14', NULL),
(41, 'app_seed_037', 12, 13, 'shortlisted', '2026-05-28 11:36:00', '2026-05-28 11:36:00', '2026-09-11 05:33:14', NULL),
(42, 'app_seed_038', 12, 7, 'shortlisted', '2026-05-28 11:37:00', '2026-05-28 11:37:00', '2026-09-11 05:33:14', NULL),
(43, 'app_seed_039', 12, 14, 'submitted', '2026-05-28 11:38:00', '2026-05-28 11:38:00', '2026-09-11 05:33:14', NULL),
(44, 'app_seed_040', 12, 20, 'interview', '2026-05-28 11:39:00', '2026-05-28 11:39:00', '2026-09-11 05:33:14', NULL),
(45, 'app_seed_041', 13, 15, 'shortlisted', '2026-05-28 11:40:00', '2026-05-28 11:40:00', '2026-09-11 05:33:14', NULL),
(46, 'app_seed_042', 13, 1, 'submitted', '2026-05-28 11:41:00', '2026-05-28 11:41:00', '2026-09-11 05:33:14', NULL),
(47, 'app_seed_043', 13, 16, 'submitted', '2026-05-28 11:42:00', '2026-05-28 11:42:00', '2026-09-11 05:33:14', NULL),
(48, 'app_seed_044', 13, 10, 'submitted', '2026-05-28 11:43:00', '2026-05-28 11:43:00', '2026-09-11 05:33:14', NULL),
(49, 'app_seed_045', 14, 20, 'shortlisted', '2026-05-28 11:44:00', '2026-05-28 11:44:00', '2026-09-11 05:33:14', NULL),
(50, 'app_seed_046', 14, 2, 'submitted', '2026-05-28 11:45:00', '2026-05-28 11:45:00', '2026-09-11 05:33:14', NULL),
(51, 'app_seed_047', 14, 16, 'shortlisted', '2026-05-28 11:46:00', '2026-05-28 11:46:00', '2026-09-11 05:33:14', NULL),
(52, 'app_seed_048', 14, 7, 'submitted', '2026-05-28 11:47:00', '2026-05-28 11:47:00', '2026-09-11 05:33:14', NULL),
(53, 'app_seed_049', 2, 16, 'submitted', '2026-05-28 11:48:00', '2026-05-28 11:48:00', '2026-09-11 05:33:27', NULL),
(54, 'app_seed_050', 2, 20, 'submitted', '2026-05-28 11:49:00', '2026-05-28 11:49:00', '2026-09-11 05:33:27', NULL),
(55, 'app_seed_051', 1, 15, 'submitted', '2026-05-28 11:50:00', '2026-05-28 11:50:00', '2026-09-11 05:45:53', NULL),
(56, 'app_seed_052', 1, 10, 'submitted', '2026-05-28 11:51:00', '2026-05-28 11:51:00', '2026-09-11 05:45:53', NULL),
(58, 'app_lbschnre', 14, 1, 'submitted', '2026-08-20 08:54:46', '2026-08-20 08:54:46', '2026-09-11 05:33:14', NULL),
(60, 'app_ls4xxyei', 13, 32, 'submitted', '2026-08-20 10:09:16', '2026-08-20 10:09:16', '2026-09-11 05:33:14', NULL),
(61, 'app_ruimyf6q', 14, 32, 'submitted', '2026-08-20 10:19:05', '2026-08-20 10:19:05', '2026-09-11 05:33:14', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `sr_cv_documents`
--

CREATE TABLE `sr_cv_documents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_profile_id` bigint(20) UNSIGNED NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `stored_path` varchar(255) NOT NULL,
  `mime_type` varchar(255) DEFAULT NULL,
  `size` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `parser_result` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`parser_result`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `sr_cv_documents`
--

INSERT INTO `sr_cv_documents` (`id`, `student_profile_id`, `original_name`, `stored_path`, `mime_type`, `size`, `parser_result`, `created_at`, `updated_at`) VALUES
(8, 32, 'cv-houssem-hammami-20260820105723.pdf', 'storage/app/uploads/cv-houssem-hammami-20260820105723.pdf', NULL, 0, '{\"emails\":[\"hammami.houssem@gmail.com\"],\"phones\":[\"216 23 373 920\",\"2013-2015\",\"2011-2013\",\"2008-2011\",\"2007-2008\"],\"detected_skills\":[\"java\",\"php\",\"javascript\",\"typescript\",\"vue\",\"angular\",\"sql\",\"cloud\",\"mobile\"]}', '2026-08-20 09:57:23', '2026-08-20 09:57:23');

-- --------------------------------------------------------

--
-- Structure de la table `sr_match_scores`
--

CREATE TABLE `sr_match_scores` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `application_id` bigint(20) UNSIGNED NOT NULL,
  `score` tinyint(3) UNSIGNED NOT NULL,
  `text_similarity` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `semantic_coverage` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `matched_skills` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`matched_skills`)),
  `missing_skills` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`missing_skills`)),
  `raw_response` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`raw_response`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `sr_match_scores`
--

INSERT INTO `sr_match_scores` (`id`, `application_id`, `score`, `text_similarity`, `semantic_coverage`, `matched_skills`, `missing_skills`, `raw_response`, `created_at`, `updated_at`) VALUES
(1, 3, 83, 46, 100, '[\"data analysis\",\"machine learning\",\"nlp\",\"python\"]', '[]', '{\"score\":83,\"matched_skills\":[\"data analysis\",\"machine learning\",\"nlp\",\"python\"],\"missing_skills\":[],\"semantic_coverage\":100,\"text_similarity\":46,\"algorithm\":\"tf-idf-cosine+semantic-skill-taxonomy\",\"source\":\"python\",\"explanation\":\"Profil pertinent, avec quelques compétences à vérifier en entretien.\"}', '2026-06-17 22:39:42', '2026-09-13 09:38:09'),
(2, 2, 23, 3, 60, '[\"backend\",\"git\"]', '[\"agile\",\"java\",\"react\",\"spring boot\",\"sql\"]', '{\"score\":23,\"matched_skills\":[\"backend\",\"git\"],\"missing_skills\":[\"agile\",\"java\",\"react\",\"spring boot\",\"sql\"],\"semantic_coverage\":60,\"text_similarity\":3,\"algorithm\":\"tf-idf-cosine+semantic-skill-taxonomy\",\"source\":\"python\",\"explanation\":\"Compatibilité partielle: le profil couvre une partie du besoin.\"}', '2026-06-17 22:39:42', '2026-09-13 09:38:10'),
(3, 1, 88, 62, 100, '[\"agile\",\"backend\",\"git\",\"java\",\"react\",\"spring boot\",\"sql\"]', '[]', '{\"score\":88,\"matched_skills\":[\"agile\",\"backend\",\"git\",\"java\",\"react\",\"spring boot\",\"sql\"],\"missing_skills\":[],\"semantic_coverage\":100,\"text_similarity\":62,\"algorithm\":\"tf-idf-cosine+semantic-skill-taxonomy\",\"source\":\"python\",\"explanation\":\"Très forte compatibilité, les compétences clés sont bien couvertes.\"}', '2026-06-17 22:39:42', '2026-09-13 09:38:10'),
(5, 49, 87, 60, 100, '[\"airflow\",\"data warehouse\",\"etl\",\"python\",\"sql\"]', '[]', '{\"score\":87,\"matched_skills\":[\"airflow\",\"data warehouse\",\"etl\",\"python\",\"sql\"],\"missing_skills\":[],\"semantic_coverage\":100,\"text_similarity\":60,\"algorithm\":\"tf-idf-cosine+semantic-skill-taxonomy\",\"source\":\"python\",\"explanation\":\"Très forte compatibilité, les compétences clés sont bien couvertes.\"}', '2026-06-19 11:31:23', '2026-09-13 09:38:04'),
(6, 51, 41, 23, 100, '[\"python\",\"sql\"]', '[\"airflow\",\"data warehouse\",\"etl\"]', '{\"score\":41,\"matched_skills\":[\"python\",\"sql\"],\"missing_skills\":[\"airflow\",\"data warehouse\",\"etl\"],\"semantic_coverage\":100,\"text_similarity\":23,\"algorithm\":\"tf-idf-cosine+semantic-skill-taxonomy\",\"source\":\"python\",\"explanation\":\"Compatibilité partielle: le profil couvre une partie du besoin.\"}', '2026-06-19 11:31:23', '2026-09-13 09:38:04'),
(7, 52, 32, 13, 50, '[\"etl\",\"sql\"]', '[\"airflow\",\"data warehouse\",\"python\"]', '{\"score\":32,\"matched_skills\":[\"etl\",\"sql\"],\"missing_skills\":[\"airflow\",\"data warehouse\",\"python\"],\"semantic_coverage\":50,\"text_similarity\":13,\"algorithm\":\"tf-idf-cosine+semantic-skill-taxonomy\",\"source\":\"python\",\"explanation\":\"Compatibilité partielle: le profil couvre une partie du besoin.\"}', '2026-06-19 11:31:23', '2026-09-13 09:38:04'),
(8, 50, 38, 15, 100, '[\"python\",\"sql\"]', '[\"airflow\",\"data warehouse\",\"etl\"]', '{\"score\":38,\"matched_skills\":[\"python\",\"sql\"],\"missing_skills\":[\"airflow\",\"data warehouse\",\"etl\"],\"semantic_coverage\":100,\"text_similarity\":15,\"algorithm\":\"tf-idf-cosine+semantic-skill-taxonomy\",\"source\":\"python\",\"explanation\":\"Compatibilité partielle: le profil couvre une partie du besoin.\"}', '2026-06-19 11:31:23', '2026-09-13 09:38:04'),
(9, 47, 35, 11, 67, '[\"api rest\",\"backend\",\"sql\"]', '[\"git\",\"laravel\",\"mysql\",\"php\"]', '{\"score\":35,\"matched_skills\":[\"api rest\",\"backend\",\"sql\"],\"missing_skills\":[\"git\",\"laravel\",\"mysql\",\"php\"],\"semantic_coverage\":67,\"text_similarity\":11,\"algorithm\":\"tf-idf-cosine+semantic-skill-taxonomy\",\"source\":\"python\",\"explanation\":\"Compatibilité partielle: le profil couvre une partie du besoin.\"}', '2026-06-19 11:31:23', '2026-09-13 09:38:04'),
(10, 45, 90, 68, 100, '[\"api rest\",\"backend\",\"git\",\"laravel\",\"mysql\",\"php\",\"sql\"]', '[]', '{\"score\":90,\"matched_skills\":[\"api rest\",\"backend\",\"git\",\"laravel\",\"mysql\",\"php\",\"sql\"],\"missing_skills\":[],\"semantic_coverage\":100,\"text_similarity\":68,\"algorithm\":\"tf-idf-cosine+semantic-skill-taxonomy\",\"source\":\"python\",\"explanation\":\"Très forte compatibilité, les compétences clés sont bien couvertes.\"}', '2026-06-19 11:31:23', '2026-09-13 09:38:04'),
(11, 48, 24, 12, 33, '[\"api rest\",\"backend\"]', '[\"git\",\"laravel\",\"mysql\",\"php\",\"sql\"]', '{\"score\":24,\"matched_skills\":[\"api rest\",\"backend\"],\"missing_skills\":[\"git\",\"laravel\",\"mysql\",\"php\",\"sql\"],\"semantic_coverage\":33,\"text_similarity\":12,\"algorithm\":\"tf-idf-cosine+semantic-skill-taxonomy\",\"source\":\"python\",\"explanation\":\"Compatibilité partielle: le profil couvre une partie du besoin.\"}', '2026-06-19 11:31:23', '2026-09-13 09:38:04'),
(12, 46, 38, 10, 100, '[\"backend\",\"git\",\"sql\"]', '[\"api rest\",\"laravel\",\"mysql\",\"php\"]', '{\"score\":38,\"matched_skills\":[\"backend\",\"git\",\"sql\"],\"missing_skills\":[\"api rest\",\"laravel\",\"mysql\",\"php\"],\"semantic_coverage\":100,\"text_similarity\":10,\"algorithm\":\"tf-idf-cosine+semantic-skill-taxonomy\",\"source\":\"python\",\"explanation\":\"Compatibilité partielle: le profil couvre une partie du besoin.\"}', '2026-06-19 11:31:23', '2026-09-13 09:38:04'),
(13, 44, 25, 9, 100, '[\"sql\"]', '[\"data analysis\",\"excel\",\"finance\",\"power bi\"]', '{\"score\":25,\"matched_skills\":[\"sql\"],\"missing_skills\":[\"data analysis\",\"excel\",\"finance\",\"power bi\"],\"semantic_coverage\":100,\"text_similarity\":9,\"algorithm\":\"tf-idf-cosine+semantic-skill-taxonomy\",\"source\":\"python\",\"explanation\":\"Compatibilité partielle: le profil couvre une partie du besoin.\"}', '2026-06-19 11:31:23', '2026-09-13 09:38:04'),
(14, 43, 57, 39, 100, '[\"data analysis\",\"excel\",\"power bi\"]', '[\"finance\",\"sql\"]', '{\"score\":57,\"matched_skills\":[\"data analysis\",\"excel\",\"power bi\"],\"missing_skills\":[\"finance\",\"sql\"],\"semantic_coverage\":100,\"text_similarity\":39,\"algorithm\":\"tf-idf-cosine+semantic-skill-taxonomy\",\"source\":\"python\",\"explanation\":\"Compatibilité partielle: le profil couvre une partie du besoin.\"}', '2026-06-19 11:31:23', '2026-09-13 09:38:05'),
(15, 41, 85, 52, 100, '[\"data analysis\",\"excel\",\"finance\",\"power bi\",\"sql\"]', '[]', '{\"score\":85,\"matched_skills\":[\"data analysis\",\"excel\",\"finance\",\"power bi\",\"sql\"],\"missing_skills\":[],\"semantic_coverage\":100,\"text_similarity\":52,\"algorithm\":\"tf-idf-cosine+semantic-skill-taxonomy\",\"source\":\"python\",\"explanation\":\"Très forte compatibilité, les compétences clés sont bien couvertes.\"}', '2026-06-19 11:31:23', '2026-09-13 09:38:05'),
(16, 42, 70, 41, 100, '[\"data analysis\",\"excel\",\"power bi\",\"sql\"]', '[\"finance\"]', '{\"score\":70,\"matched_skills\":[\"data analysis\",\"excel\",\"power bi\",\"sql\"],\"missing_skills\":[\"finance\"],\"semantic_coverage\":100,\"text_similarity\":41,\"algorithm\":\"tf-idf-cosine+semantic-skill-taxonomy\",\"source\":\"python\",\"explanation\":\"Profil pertinent, avec quelques compétences à vérifier en entretien.\"}', '2026-06-19 11:31:23', '2026-09-13 09:38:05'),
(17, 40, 0, 0, 0, '[]', '[\"content marketing\",\"excel\",\"google analytics\",\"seo\",\"social media\"]', '{\"score\":0,\"matched_skills\":[],\"missing_skills\":[\"content marketing\",\"excel\",\"google analytics\",\"seo\",\"social media\"],\"semantic_coverage\":0,\"text_similarity\":0,\"algorithm\":\"tf-idf-cosine+semantic-skill-taxonomy\",\"source\":\"python\",\"explanation\":\"Faible compatibilité avec les exigences principales.\"}', '2026-06-19 11:31:23', '2026-09-13 09:38:05'),
(18, 39, 2, 5, 0, '[]', '[\"content marketing\",\"excel\",\"google analytics\",\"seo\",\"social media\"]', '{\"score\":2,\"matched_skills\":[],\"missing_skills\":[\"content marketing\",\"excel\",\"google analytics\",\"seo\",\"social media\"],\"semantic_coverage\":0,\"text_similarity\":5,\"algorithm\":\"tf-idf-cosine+semantic-skill-taxonomy\",\"source\":\"python\",\"explanation\":\"Faible compatibilité avec les exigences principales.\"}', '2026-06-19 11:31:23', '2026-09-13 09:38:05'),
(19, 37, 77, 58, 0, '[\"content marketing\",\"excel\",\"google analytics\",\"seo\",\"social media\"]', '[]', '{\"score\":77,\"matched_skills\":[\"content marketing\",\"excel\",\"google analytics\",\"seo\",\"social media\"],\"missing_skills\":[],\"semantic_coverage\":0,\"text_similarity\":58,\"algorithm\":\"tf-idf-cosine+semantic-skill-taxonomy\",\"source\":\"python\",\"explanation\":\"Profil pertinent, avec quelques compétences à vérifier en entretien.\"}', '2026-06-19 11:31:23', '2026-09-13 09:38:05'),
(20, 38, 0, 0, 0, '[]', '[\"content marketing\",\"excel\",\"google analytics\",\"seo\",\"social media\"]', '{\"score\":0,\"matched_skills\":[],\"missing_skills\":[\"content marketing\",\"excel\",\"google analytics\",\"seo\",\"social media\"],\"semantic_coverage\":0,\"text_similarity\":0,\"algorithm\":\"tf-idf-cosine+semantic-skill-taxonomy\",\"source\":\"python\",\"explanation\":\"Faible compatibilité avec les exigences principales.\"}', '2026-06-19 11:31:23', '2026-09-13 09:38:05'),
(21, 34, 24, 7, 100, '[\"sql\"]', '[\"abap\",\"business process\",\"erp\",\"sap\"]', '{\"score\":24,\"matched_skills\":[\"sql\"],\"missing_skills\":[\"abap\",\"business process\",\"erp\",\"sap\"],\"semantic_coverage\":100,\"text_similarity\":7,\"algorithm\":\"tf-idf-cosine+semantic-skill-taxonomy\",\"source\":\"python\",\"explanation\":\"Compatibilité partielle: le profil couvre une partie du besoin.\"}', '2026-06-19 11:31:23', '2026-09-13 09:38:05'),
(22, 36, 10, 0, 100, '[]', '[\"abap\",\"business process\",\"erp\",\"sap\",\"sql\"]', '{\"score\":10,\"matched_skills\":[],\"missing_skills\":[\"abap\",\"business process\",\"erp\",\"sap\",\"sql\"],\"semantic_coverage\":100,\"text_similarity\":0,\"algorithm\":\"tf-idf-cosine+semantic-skill-taxonomy\",\"source\":\"python\",\"explanation\":\"Faible compatibilité avec les exigences principales.\"}', '2026-06-19 11:31:23', '2026-09-13 09:38:05'),
(23, 33, 85, 53, 100, '[\"abap\",\"business process\",\"erp\",\"sap\",\"sql\"]', '[]', '{\"score\":85,\"matched_skills\":[\"abap\",\"business process\",\"erp\",\"sap\",\"sql\"],\"missing_skills\":[],\"semantic_coverage\":100,\"text_similarity\":53,\"algorithm\":\"tf-idf-cosine+semantic-skill-taxonomy\",\"source\":\"python\",\"explanation\":\"Très forte compatibilité, les compétences clés sont bien couvertes.\"}', '2026-06-19 11:31:23', '2026-09-13 09:38:05'),
(24, 35, 23, 4, 100, '[\"sql\"]', '[\"abap\",\"business process\",\"erp\",\"sap\"]', '{\"score\":23,\"matched_skills\":[\"sql\"],\"missing_skills\":[\"abap\",\"business process\",\"erp\",\"sap\"],\"semantic_coverage\":100,\"text_similarity\":4,\"algorithm\":\"tf-idf-cosine+semantic-skill-taxonomy\",\"source\":\"python\",\"explanation\":\"Compatibilité partielle: le profil couvre une partie du besoin.\"}', '2026-06-19 11:31:23', '2026-09-13 09:38:06'),
(25, 31, 30, 4, 100, '[\"api rest\",\"backend\"]', '[\"java\",\"jira\",\"selenium\",\"testing\"]', '{\"score\":30,\"matched_skills\":[\"api rest\",\"backend\"],\"missing_skills\":[\"java\",\"jira\",\"selenium\",\"testing\"],\"semantic_coverage\":100,\"text_similarity\":4,\"algorithm\":\"tf-idf-cosine+semantic-skill-taxonomy\",\"source\":\"python\",\"explanation\":\"Compatibilité partielle: le profil couvre une partie du besoin.\"}', '2026-06-19 11:31:23', '2026-09-13 09:38:06'),
(26, 30, 31, 5, 100, '[\"api rest\",\"backend\"]', '[\"java\",\"jira\",\"selenium\",\"testing\"]', '{\"score\":31,\"matched_skills\":[\"api rest\",\"backend\"],\"missing_skills\":[\"java\",\"jira\",\"selenium\",\"testing\"],\"semantic_coverage\":100,\"text_similarity\":5,\"algorithm\":\"tf-idf-cosine+semantic-skill-taxonomy\",\"source\":\"python\",\"explanation\":\"Compatibilité partielle: le profil couvre une partie du besoin.\"}', '2026-06-19 11:31:23', '2026-09-13 09:38:06'),
(27, 29, 83, 48, 100, '[\"api rest\",\"backend\",\"java\",\"jira\",\"selenium\",\"testing\"]', '[]', '{\"score\":83,\"matched_skills\":[\"api rest\",\"backend\",\"java\",\"jira\",\"selenium\",\"testing\"],\"missing_skills\":[],\"semantic_coverage\":100,\"text_similarity\":48,\"algorithm\":\"tf-idf-cosine+semantic-skill-taxonomy\",\"source\":\"python\",\"explanation\":\"Très forte compatibilité, les compétences clés sont bien couvertes.\"}', '2026-06-19 11:31:23', '2026-09-13 09:38:06'),
(28, 32, 32, 9, 100, '[\"backend\",\"java\"]', '[\"api rest\",\"jira\",\"selenium\",\"testing\"]', '{\"score\":32,\"matched_skills\":[\"backend\",\"java\"],\"missing_skills\":[\"api rest\",\"jira\",\"selenium\",\"testing\"],\"semantic_coverage\":100,\"text_similarity\":9,\"algorithm\":\"tf-idf-cosine+semantic-skill-taxonomy\",\"source\":\"python\",\"explanation\":\"Compatibilité partielle: le profil couvre une partie du besoin.\"}', '2026-06-19 11:31:23', '2026-09-13 09:38:06'),
(29, 27, 0, 0, 0, '[]', '[\"arduino\",\"c\",\"c++\",\"embedded\",\"iot\"]', '{\"score\":0,\"matched_skills\":[],\"missing_skills\":[\"arduino\",\"c\",\"c++\",\"embedded\",\"iot\"],\"semantic_coverage\":0,\"text_similarity\":0,\"algorithm\":\"tf-idf-cosine+semantic-skill-taxonomy\",\"source\":\"python\",\"explanation\":\"Faible compatibilité avec les exigences principales.\"}', '2026-06-19 11:31:23', '2026-09-13 09:38:06'),
(30, 28, 0, 0, 0, '[]', '[\"arduino\",\"c\",\"c++\",\"embedded\",\"iot\"]', '{\"score\":0,\"matched_skills\":[],\"missing_skills\":[\"arduino\",\"c\",\"c++\",\"embedded\",\"iot\"],\"semantic_coverage\":0,\"text_similarity\":0,\"algorithm\":\"tf-idf-cosine+semantic-skill-taxonomy\",\"source\":\"python\",\"explanation\":\"Faible compatibilité avec les exigences principales.\"}', '2026-06-19 11:31:23', '2026-09-13 09:38:06'),
(31, 26, 1, 2, 0, '[]', '[\"arduino\",\"c\",\"c++\",\"embedded\",\"iot\"]', '{\"score\":1,\"matched_skills\":[],\"missing_skills\":[\"arduino\",\"c\",\"c++\",\"embedded\",\"iot\"],\"semantic_coverage\":0,\"text_similarity\":2,\"algorithm\":\"tf-idf-cosine+semantic-skill-taxonomy\",\"source\":\"python\",\"explanation\":\"Faible compatibilité avec les exigences principales.\"}', '2026-06-19 11:31:23', '2026-09-13 09:38:06'),
(32, 25, 71, 42, 0, '[\"arduino\",\"c\",\"c++\",\"embedded\",\"iot\"]', '[]', '{\"score\":71,\"matched_skills\":[\"arduino\",\"c\",\"c++\",\"embedded\",\"iot\"],\"missing_skills\":[],\"semantic_coverage\":0,\"text_similarity\":42,\"algorithm\":\"tf-idf-cosine+semantic-skill-taxonomy\",\"source\":\"python\",\"explanation\":\"Profil pertinent, avec quelques compétences à vérifier en entretien.\"}', '2026-06-19 11:31:23', '2026-09-13 09:38:06'),
(33, 22, 2, 5, 0, '[]', '[\"figma\",\"frontend\",\"prototyping\",\"ui\",\"user research\",\"ux\"]', '{\"score\":2,\"matched_skills\":[],\"missing_skills\":[\"figma\",\"frontend\",\"prototyping\",\"ui\",\"user research\",\"ux\"],\"semantic_coverage\":0,\"text_similarity\":5,\"algorithm\":\"tf-idf-cosine+semantic-skill-taxonomy\",\"source\":\"python\",\"explanation\":\"Faible compatibilité avec les exigences principales.\"}', '2026-06-19 11:31:23', '2026-09-13 09:38:07'),
(34, 23, 0, 0, 0, '[]', '[\"figma\",\"frontend\",\"prototyping\",\"ui\",\"user research\",\"ux\"]', '{\"score\":0,\"matched_skills\":[],\"missing_skills\":[\"figma\",\"frontend\",\"prototyping\",\"ui\",\"user research\",\"ux\"],\"semantic_coverage\":0,\"text_similarity\":0,\"algorithm\":\"tf-idf-cosine+semantic-skill-taxonomy\",\"source\":\"python\",\"explanation\":\"Faible compatibilité avec les exigences principales.\"}', '2026-06-19 11:31:23', '2026-09-13 09:38:07'),
(35, 21, 80, 39, 100, '[\"figma\",\"frontend\",\"prototyping\",\"ui\",\"user research\",\"ux\"]', '[]', '{\"score\":80,\"matched_skills\":[\"figma\",\"frontend\",\"prototyping\",\"ui\",\"user research\",\"ux\"],\"missing_skills\":[],\"semantic_coverage\":100,\"text_similarity\":39,\"algorithm\":\"tf-idf-cosine+semantic-skill-taxonomy\",\"source\":\"python\",\"explanation\":\"Profil pertinent, avec quelques compétences à vérifier en entretien.\"}', '2026-06-19 11:31:23', '2026-09-13 09:38:07'),
(36, 24, 0, 0, 0, '[]', '[\"figma\",\"frontend\",\"prototyping\",\"ui\",\"user research\",\"ux\"]', '{\"score\":0,\"matched_skills\":[],\"missing_skills\":[\"figma\",\"frontend\",\"prototyping\",\"ui\",\"user research\",\"ux\"],\"semantic_coverage\":0,\"text_similarity\":0,\"algorithm\":\"tf-idf-cosine+semantic-skill-taxonomy\",\"source\":\"python\",\"explanation\":\"Faible compatibilité avec les exigences principales.\"}', '2026-06-19 11:31:23', '2026-09-13 09:38:07'),
(37, 20, 37, 12, 100, '[\"etl\",\"sql\"]', '[\"data analysis\",\"excel\",\"power bi\"]', '{\"score\":37,\"matched_skills\":[\"etl\",\"sql\"],\"missing_skills\":[\"data analysis\",\"excel\",\"power bi\"],\"semantic_coverage\":100,\"text_similarity\":12,\"algorithm\":\"tf-idf-cosine+semantic-skill-taxonomy\",\"source\":\"python\",\"explanation\":\"Compatibilité partielle: le profil couvre une partie du besoin.\"}', '2026-06-19 11:31:23', '2026-09-13 09:38:07'),
(38, 19, 54, 29, 100, '[\"data analysis\",\"excel\",\"power bi\"]', '[\"etl\",\"sql\"]', '{\"score\":54,\"matched_skills\":[\"data analysis\",\"excel\",\"power bi\"],\"missing_skills\":[\"etl\",\"sql\"],\"semantic_coverage\":100,\"text_similarity\":29,\"algorithm\":\"tf-idf-cosine+semantic-skill-taxonomy\",\"source\":\"python\",\"explanation\":\"Compatibilité partielle: le profil couvre une partie du besoin.\"}', '2026-06-19 11:31:23', '2026-09-13 09:38:07'),
(39, 18, 67, 34, 100, '[\"data analysis\",\"excel\",\"power bi\",\"sql\"]', '[\"etl\"]', '{\"score\":67,\"matched_skills\":[\"data analysis\",\"excel\",\"power bi\",\"sql\"],\"missing_skills\":[\"etl\"],\"semantic_coverage\":100,\"text_similarity\":34,\"algorithm\":\"tf-idf-cosine+semantic-skill-taxonomy\",\"source\":\"python\",\"explanation\":\"Profil pertinent, avec quelques compétences à vérifier en entretien.\"}', '2026-06-19 11:31:23', '2026-09-13 09:38:07'),
(40, 17, 87, 60, 100, '[\"data analysis\",\"etl\",\"excel\",\"power bi\",\"sql\"]', '[]', '{\"score\":87,\"matched_skills\":[\"data analysis\",\"etl\",\"excel\",\"power bi\",\"sql\"],\"missing_skills\":[],\"semantic_coverage\":100,\"text_similarity\":60,\"algorithm\":\"tf-idf-cosine+semantic-skill-taxonomy\",\"source\":\"python\",\"explanation\":\"Très forte compatibilité, les compétences clés sont bien couvertes.\"}', '2026-06-19 11:31:23', '2026-09-13 09:38:07'),
(41, 14, 56, 24, 100, '[\"api rest\",\"backend\",\"firebase\",\"mobile\"]', '[\"dart\",\"flutter\"]', '{\"score\":56,\"matched_skills\":[\"api rest\",\"backend\",\"firebase\",\"mobile\"],\"missing_skills\":[\"dart\",\"flutter\"],\"semantic_coverage\":100,\"text_similarity\":24,\"algorithm\":\"tf-idf-cosine+semantic-skill-taxonomy\",\"source\":\"python\",\"explanation\":\"Profil pertinent, avec quelques compétences à vérifier en entretien.\"}', '2026-06-19 11:31:23', '2026-09-13 09:38:07'),
(42, 16, 0, 0, 0, '[]', '[\"api rest\",\"backend\",\"dart\",\"firebase\",\"flutter\",\"mobile\"]', '{\"score\":0,\"matched_skills\":[],\"missing_skills\":[\"api rest\",\"backend\",\"dart\",\"firebase\",\"flutter\",\"mobile\"],\"semantic_coverage\":0,\"text_similarity\":0,\"algorithm\":\"tf-idf-cosine+semantic-skill-taxonomy\",\"source\":\"python\",\"explanation\":\"Faible compatibilité avec les exigences principales.\"}', '2026-06-19 11:31:23', '2026-09-13 09:38:08'),
(43, 13, 85, 52, 100, '[\"api rest\",\"backend\",\"dart\",\"firebase\",\"flutter\",\"mobile\"]', '[]', '{\"score\":85,\"matched_skills\":[\"api rest\",\"backend\",\"dart\",\"firebase\",\"flutter\",\"mobile\"],\"missing_skills\":[],\"semantic_coverage\":100,\"text_similarity\":52,\"algorithm\":\"tf-idf-cosine+semantic-skill-taxonomy\",\"source\":\"python\",\"explanation\":\"Très forte compatibilité, les compétences clés sont bien couvertes.\"}', '2026-06-19 11:31:23', '2026-09-13 09:38:08'),
(44, 15, 20, 2, 100, '[\"backend\"]', '[\"api rest\",\"dart\",\"firebase\",\"flutter\",\"mobile\"]', '{\"score\":20,\"matched_skills\":[\"backend\"],\"missing_skills\":[\"api rest\",\"dart\",\"firebase\",\"flutter\",\"mobile\"],\"semantic_coverage\":100,\"text_similarity\":2,\"algorithm\":\"tf-idf-cosine+semantic-skill-taxonomy\",\"source\":\"python\",\"explanation\":\"Compatibilité partielle: le profil couvre une partie du besoin.\"}', '2026-06-19 11:31:23', '2026-09-13 09:38:08'),
(45, 12, 0, 0, 0, '[]', '[\"aws\",\"ci/cd\",\"devops\",\"docker\",\"kubernetes\",\"linux\"]', '{\"score\":0,\"matched_skills\":[],\"missing_skills\":[\"aws\",\"ci/cd\",\"devops\",\"docker\",\"kubernetes\",\"linux\"],\"semantic_coverage\":0,\"text_similarity\":0,\"algorithm\":\"tf-idf-cosine+semantic-skill-taxonomy\",\"source\":\"python\",\"explanation\":\"Faible compatibilité avec les exigences principales.\"}', '2026-06-19 11:31:23', '2026-09-13 09:38:08'),
(46, 10, 33, 12, 100, '[\"devops\",\"docker\"]', '[\"aws\",\"ci/cd\",\"kubernetes\",\"linux\"]', '{\"score\":33,\"matched_skills\":[\"devops\",\"docker\"],\"missing_skills\":[\"aws\",\"ci/cd\",\"kubernetes\",\"linux\"],\"semantic_coverage\":100,\"text_similarity\":12,\"algorithm\":\"tf-idf-cosine+semantic-skill-taxonomy\",\"source\":\"python\",\"explanation\":\"Compatibilité partielle: le profil couvre une partie du besoin.\"}', '2026-06-19 11:31:23', '2026-09-13 09:38:08'),
(47, 11, 0, 0, 0, '[]', '[\"aws\",\"ci/cd\",\"devops\",\"docker\",\"kubernetes\",\"linux\"]', '{\"score\":0,\"matched_skills\":[],\"missing_skills\":[\"aws\",\"ci/cd\",\"devops\",\"docker\",\"kubernetes\",\"linux\"],\"semantic_coverage\":0,\"text_similarity\":0,\"algorithm\":\"tf-idf-cosine+semantic-skill-taxonomy\",\"source\":\"python\",\"explanation\":\"Faible compatibilité avec les exigences principales.\"}', '2026-06-19 11:31:23', '2026-09-13 09:38:08'),
(48, 9, 89, 65, 100, '[\"aws\",\"ci/cd\",\"devops\",\"docker\",\"kubernetes\",\"linux\"]', '[]', '{\"score\":89,\"matched_skills\":[\"aws\",\"ci/cd\",\"devops\",\"docker\",\"kubernetes\",\"linux\"],\"missing_skills\":[],\"semantic_coverage\":100,\"text_similarity\":65,\"algorithm\":\"tf-idf-cosine+semantic-skill-taxonomy\",\"source\":\"python\",\"explanation\":\"Très forte compatibilité, les compétences clés sont bien couvertes.\"}', '2026-06-19 11:31:23', '2026-09-13 09:38:08'),
(49, 8, 1, 2, 0, '[]', '[\"cybersecurity\",\"linux\",\"network\",\"python\",\"siem\"]', '{\"score\":1,\"matched_skills\":[],\"missing_skills\":[\"cybersecurity\",\"linux\",\"network\",\"python\",\"siem\"],\"semantic_coverage\":0,\"text_similarity\":2,\"algorithm\":\"tf-idf-cosine+semantic-skill-taxonomy\",\"source\":\"python\",\"explanation\":\"Compatibilité partielle: le profil couvre une partie du besoin.\"}', '2026-06-19 11:31:23', '2026-09-13 09:38:08'),
(50, 6, 26, 8, 0, '[\"linux\",\"network\"]', '[\"cybersecurity\",\"python\",\"siem\"]', '{\"score\":26,\"matched_skills\":[\"linux\",\"network\"],\"missing_skills\":[\"cybersecurity\",\"python\",\"siem\"],\"semantic_coverage\":0,\"text_similarity\":8,\"algorithm\":\"tf-idf-cosine+semantic-skill-taxonomy\",\"source\":\"python\",\"explanation\":\"Compatibilité partielle: le profil couvre une partie du besoin.\"}', '2026-06-19 11:31:23', '2026-09-13 09:38:08'),
(51, 7, 12, 1, 0, '[\"linux\"]', '[\"cybersecurity\",\"network\",\"python\",\"siem\"]', '{\"score\":12,\"matched_skills\":[\"linux\"],\"missing_skills\":[\"cybersecurity\",\"network\",\"python\",\"siem\"],\"semantic_coverage\":0,\"text_similarity\":1,\"algorithm\":\"tf-idf-cosine+semantic-skill-taxonomy\",\"source\":\"python\",\"explanation\":\"Compatibilité partielle: le profil couvre une partie du besoin.\"}', '2026-06-19 11:31:23', '2026-09-13 09:38:09'),
(52, 5, 83, 46, 100, '[\"cybersecurity\",\"linux\",\"network\",\"python\",\"siem\"]', '[]', '{\"score\":83,\"matched_skills\":[\"cybersecurity\",\"linux\",\"network\",\"python\",\"siem\"],\"missing_skills\":[],\"semantic_coverage\":100,\"text_similarity\":46,\"algorithm\":\"tf-idf-cosine+semantic-skill-taxonomy\",\"source\":\"python\",\"explanation\":\"Très forte compatibilité, les compétences clés sont bien couvertes.\"}', '2026-06-19 11:31:23', '2026-09-13 09:38:09'),
(53, 54, 22, 2, 67, '[\"python\"]', '[\"data analysis\",\"machine learning\",\"nlp\"]', '{\"score\":22,\"matched_skills\":[\"python\"],\"missing_skills\":[\"data analysis\",\"machine learning\",\"nlp\"],\"semantic_coverage\":67,\"text_similarity\":2,\"algorithm\":\"tf-idf-cosine+semantic-skill-taxonomy\",\"source\":\"python\",\"explanation\":\"Compatibilité partielle: le profil couvre une partie du besoin.\"}', '2026-06-19 11:31:23', '2026-09-13 09:38:09'),
(54, 53, 23, 7, 67, '[\"python\"]', '[\"data analysis\",\"machine learning\",\"nlp\"]', '{\"score\":23,\"matched_skills\":[\"python\"],\"missing_skills\":[\"data analysis\",\"machine learning\",\"nlp\"],\"semantic_coverage\":67,\"text_similarity\":7,\"algorithm\":\"tf-idf-cosine+semantic-skill-taxonomy\",\"source\":\"python\",\"explanation\":\"Compatibilité partielle: le profil couvre une partie du besoin.\"}', '2026-06-19 11:31:23', '2026-09-13 09:38:09'),
(55, 55, 34, 10, 60, '[\"backend\",\"git\",\"sql\"]', '[\"agile\",\"java\",\"react\",\"spring boot\"]', '{\"score\":34,\"matched_skills\":[\"backend\",\"git\",\"sql\"],\"missing_skills\":[\"agile\",\"java\",\"react\",\"spring boot\"],\"semantic_coverage\":60,\"text_similarity\":10,\"algorithm\":\"tf-idf-cosine+semantic-skill-taxonomy\",\"source\":\"python\",\"explanation\":\"Compatibilité partielle: le profil couvre une partie du besoin.\"}', '2026-06-19 11:31:23', '2026-09-13 09:38:09'),
(56, 56, 24, 16, 20, '[\"backend\",\"java\"]', '[\"agile\",\"git\",\"react\",\"spring boot\",\"sql\"]', '{\"score\":24,\"matched_skills\":[\"backend\",\"java\"],\"missing_skills\":[\"agile\",\"git\",\"react\",\"spring boot\",\"sql\"],\"semantic_coverage\":20,\"text_similarity\":16,\"algorithm\":\"tf-idf-cosine+semantic-skill-taxonomy\",\"source\":\"python\",\"explanation\":\"Compatibilité partielle: le profil couvre une partie du besoin.\"}', '2026-06-19 11:31:23', '2026-09-13 09:38:09'),
(58, 58, 23, 4, 100, '[\"sql\"]', '[\"airflow\",\"data warehouse\",\"etl\",\"python\"]', '{\"score\":23,\"matched_skills\":[\"sql\"],\"missing_skills\":[\"airflow\",\"data warehouse\",\"etl\",\"python\"],\"semantic_coverage\":100,\"text_similarity\":4,\"algorithm\":\"tf-idf-cosine+semantic-skill-taxonomy\",\"source\":\"python\",\"explanation\":\"Compatibilité partielle: le profil couvre une partie du besoin.\"}', '2026-08-20 09:13:03', '2026-09-13 09:38:04'),
(60, 60, 43, 11, 67, '[\"laravel\",\"mysql\",\"php\",\"sql\"]', '[\"api rest\",\"backend\",\"git\"]', '{\"score\":43,\"matched_skills\":[\"laravel\",\"mysql\",\"php\",\"sql\"],\"missing_skills\":[\"api rest\",\"backend\",\"git\"],\"semantic_coverage\":67,\"text_similarity\":11,\"algorithm\":\"tf-idf-cosine+semantic-skill-taxonomy\",\"source\":\"python\",\"explanation\":\"Compatibilité partielle: le profil couvre une partie du besoin.\"}', '2026-08-20 10:09:25', '2026-09-13 09:38:04'),
(61, 61, 34, 4, 100, '[\"python\",\"sql\"]', '[\"airflow\",\"data warehouse\",\"etl\"]', '{\"score\":34,\"matched_skills\":[\"python\",\"sql\"],\"missing_skills\":[\"airflow\",\"data warehouse\",\"etl\"],\"semantic_coverage\":100,\"text_similarity\":4,\"algorithm\":\"tf-idf-cosine+semantic-skill-taxonomy\",\"source\":\"python\",\"explanation\":\"Compatibilité partielle: le profil couvre une partie du besoin.\"}', '2026-08-20 10:19:10', '2026-09-13 09:38:03');

-- --------------------------------------------------------

--
-- Structure de la table `sr_offers`
--

CREATE TABLE `sr_offers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `public_id` varchar(255) NOT NULL,
  `recruiter_profile_id` bigint(20) UNSIGNED DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `company` varchar(255) NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'Stage',
  `description` longtext NOT NULL,
  `required_skills` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`required_skills`)),
  `status` enum('draft','published','closed') NOT NULL DEFAULT 'published',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp(6) NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `sr_offers`
--

INSERT INTO `sr_offers` (`id`, `public_id`, `recruiter_profile_id`, `title`, `company`, `location`, `type`, `description`, `required_skills`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'off_java_react', 1, 'Stage PFE Développeur Full-Stack Java / React', 'Smart Business Solutions', 'Tunis hybride', 'Stage PFE', 'Nous cherchons un stagiaire capable de participer à une plateforme SaaS. Le profil doit comprendre Java Enterprise, Spring Boot, API REST, ReactJS, SQL, Git et méthodes Agile.', '[\"java\",\"spring boot\",\"react\",\"sql\",\"git\",\"agile\"]', 'published', '2026-05-27 09:00:00', '2026-09-11 05:45:53', NULL),
(2, 'off_nlp', 1, 'Stage Matching CV-Offres NLP', 'Smart-Recruit Lab', 'Tunis', 'Stage recherche appliquée', 'Mission NLP: parsing de CV PDF, TF-IDF, similarité cosinus, extraction de compétences, expérimentation BERT et tableau de bord de recommandations.', '[\"python\",\"nlp\",\"machine learning\",\"data analysis\"]', 'published', '2026-05-27 09:15:00', '2026-09-11 11:06:45', NULL),
(3, 'off_cyber_soc', 1, 'Stage SOC Analyst Cybersecurite', 'SecureNet Tunisia', 'Tunis', 'Stage PFE', 'Surveillance securite, analyse logs, qualification incidents, documentation procedures SOC et tableaux de bord SIEM.', '[\"cybersecurity\",\"linux\",\"network\",\"siem\",\"python\"]', 'published', '2026-05-28 10:00:00', '2026-09-11 05:33:14', NULL),
(4, 'off_devops_cloud', 1, 'Stage DevOps Cloud Docker Kubernetes', 'CloudOps Factory', 'Ariana hybride', 'Stage PFE', 'Automatiser le deploiement applicatif, mettre en place CI/CD, containers Docker, orchestration Kubernetes et monitoring.', '[\"docker\",\"kubernetes\",\"ci/cd\",\"linux\",\"aws\"]', 'published', '2026-05-28 10:05:00', '2026-09-11 05:33:14', NULL),
(5, 'off_mobile_flutter', 1, 'Stage Developpement Mobile Flutter Firebase', 'Mobility Lab', 'Sousse', 'Stage', 'Developper une application mobile Flutter connectee a Firebase et a des API REST pour le suivi client.', '[\"flutter\",\"dart\",\"firebase\",\"mobile\",\"api rest\"]', 'published', '2026-05-28 10:10:00', '2026-09-11 05:33:14', NULL),
(6, 'off_bi_powerbi', 1, 'Stage Business Intelligence Power BI', 'DataViz Consulting', 'Tunis', 'Stage PFE', 'Construire des dashboards Power BI, modeliser des donnees SQL, preparer ETL et indicateurs de pilotage.', '[\"power bi\",\"sql\",\"etl\",\"data analysis\",\"excel\"]', 'published', '2026-05-28 10:15:00', '2026-09-11 05:33:14', NULL),
(7, 'off_ux_ui', 1, 'Stage UX UI Designer Produit SaaS', 'Studio Product', 'Nabeul remote', 'Stage', 'Concevoir des parcours utilisateurs, wireframes, maquettes Figma, prototypes et tests utilisateurs pour un produit SaaS.', '[\"figma\",\"ux\",\"ui\",\"user research\",\"prototyping\"]', 'published', '2026-05-28 10:20:00', '2026-09-11 10:43:04', NULL),
(8, 'off_iot_embedded', 1, 'Stage IoT Systemes Embarques ESP32', 'IoT Engineering', 'Sfax', 'Stage PFE', 'Developper un prototype IoT avec capteurs, firmware embarque, communication MQTT et collecte de donnees.', '[\"c\",\"c++\",\"iot\",\"arduino\",\"embedded\"]', 'published', '2026-05-28 10:25:00', '2026-09-11 10:59:42', NULL),
(9, 'off_qa_automation', 1, 'Stage QA Automation Selenium API', 'Quality First', 'Tunis', 'Stage', 'Automatiser des tests web Selenium, valider des API REST, suivre les anomalies Jira et rediger les plans de test.', '[\"selenium\",\"testing\",\"java\",\"api rest\",\"jira\"]', 'published', '2026-05-28 10:30:00', '2026-09-11 05:33:14', NULL),
(10, 'off_sap_abap', 1, 'Stage Consultant SAP ABAP ERP', 'ERP Advisory', 'Tunis', 'Stage PFE', 'Participer aux developpements ABAP, analyses processus metier et support ERP sur modules achat vente.', '[\"sap\",\"abap\",\"sql\",\"erp\",\"business process\"]', 'published', '2026-05-28 10:35:00', '2026-09-11 05:33:14', NULL),
(11, 'off_marketing_seo', 1, 'Stage Marketing Digital SEO Analytics', 'Growth Media', 'Monastir', 'Stage', 'Optimiser le referencement, suivre Google Analytics, produire contenu, reporting social media et recommandations acquisition.', '[\"seo\",\"google analytics\",\"content marketing\",\"social media\",\"excel\"]', 'published', '2026-05-28 10:40:00', '2026-09-11 05:33:14', NULL),
(12, 'off_finance_data', 1, 'Stage Finance Data Reporting Power BI', 'FinanceLab', 'Tunis', 'Stage PFE', 'Automatiser les reportings financiers, construire des tableaux Power BI et analyser des donnees Excel SQL.', '[\"finance\",\"excel\",\"power bi\",\"sql\",\"data analysis\"]', 'published', '2026-05-28 10:45:00', '2026-09-11 05:33:14', NULL),
(13, 'off_laravel_api', 1, 'Stage Developpeur Backend Laravel API', 'WebFactory', 'Tunis', 'Stage PFE', 'Developper des modules Laravel, API REST, MySQL, authentification, tests et integration front-end.', '[\"php\",\"laravel\",\"mysql\",\"api rest\",\"git\"]', 'published', '2026-05-28 10:50:00', '2026-09-11 05:33:14', NULL),
(14, 'off_data_engineering', 1, 'Stage Data Engineering ETL Airflow', 'DataWorks', 'Tunis hybride', 'Stage PFE', 'Construire des pipelines ETL, orchestrer Airflow, nettoyer des donnees SQL et alimenter un data warehouse.', '[\"python\",\"etl\",\"sql\",\"airflow\",\"data warehouse\"]', 'published', '2026-05-28 10:55:00', '2026-09-11 05:33:14', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `sr_recruiter_profiles`
--

CREATE TABLE `sr_recruiter_profiles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `public_id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `position` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp(6) NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `sr_recruiter_profiles`
--

INSERT INTO `sr_recruiter_profiles` (`id`, `public_id`, `user_id`, `company_name`, `position`, `website`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'rec_demo', 2, 'Smart Business Solutions', 'Talent Manager', 'https://smartbs.tn', '2026-06-17 22:39:41', '2026-09-11 05:33:14', NULL),
(3, 'rec_txppvvuy', 1, 'Admin Smart-Recruit', 'Recruteur', NULL, '2026-08-19 08:59:32', '2026-08-19 08:59:32', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `sr_student_profiles`
--

CREATE TABLE `sr_student_profiles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `public_id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `headline` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `education` varchar(255) DEFAULT NULL,
  `experience_years` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `skills` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`skills`)),
  `links` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`links`)),
  `cv_text` longtext DEFAULT NULL,
  `cv_metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`cv_metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp(6) NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `sr_student_profiles`
--

INSERT INTO `sr_student_profiles` (`id`, `public_id`, `user_id`, `headline`, `location`, `education`, `experience_years`, `skills`, `links`, `cv_text`, `cv_metadata`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'stu_amira', 3, 'Développeuse Full-Stack Java / React', 'Tunis', 'Master Génie Logiciel', 2, '[\"java\",\"spring boot\",\"react\",\"sql\",\"git\",\"agile\"]', '[]', 'Master Génie Logiciel. Expérience J2EE, Spring Boot, REST API, ReactJS, SQL, Git, Scrum. Réalisation d une plateforme de gestion de stages avec tableaux de bord.', '{\"emails\":[\"amira.bensalem@example.com\"],\"phones\":[],\"detected_skills\":[\"java\",\"react\",\"sql\",\"git\",\"agile\"]}', '2026-05-27 08:00:00', '2026-09-11 14:36:06', NULL),
(2, 'stu_yassine', 4, 'Data Scientist Junior NLP', 'Sfax', 'Mastère Data Science', 1, '[\"python\",\"machine learning\",\"nlp\",\"sql\",\"data analysis\"]', '{\"github\":\"https://github.com/yassine-demo\",\"linkedin\":\"https://linkedin.com/in/yassine-demo\"}', 'Mastère Data Science. Projets Python, scikit-learn, TF-IDF, NLP, analyse de données, extraction d entités avec spaCy, dashboards Power BI.', '{\"emails\":[\"yassine.trabelsi@example.com\"],\"phones\":[],\"detected_skills\":[\"python\",\"machine learning\",\"nlp\",\"data analysis\"]}', '2026-05-27 08:10:00', '2026-09-11 05:33:14', NULL),
(3, 'stu_ines', 5, 'Développeuse Front-End Vue / UI', 'Ariana', 'Licence Informatique', 1, '[\"javascript\",\"typescript\",\"vue\",\"frontend\",\"git\"]', '{\"github\":\"https://github.com/ines-demo\"}', 'Licence Informatique. Développement front-end avec Vue.js, TypeScript, composants UI, intégration API REST, tests utilisateurs et Git.', '{\"emails\":[\"ines.kammoun@example.com\"],\"phones\":[],\"detected_skills\":[\"javascript\",\"typescript\",\"vue\",\"frontend\",\"git\"]}', '2026-05-27 08:20:00', '2026-09-11 10:43:05', NULL),
(4, 'stu_selim_cyber', 6, 'Analyste Cybersecurite SOC', 'Tunis', 'Licence Securite Informatique', 1, '[\"cybersecurity\",\"linux\",\"network\",\"siem\",\"python\"]', '[]', 'Licence securite informatique. Stage SOC niveau 1, analyse logs Linux, detection incidents, SIEM, reseau TCP IP, scripts Python pour automatiser des alertes.', '{\"emails\":[\"selim.mansouri@example.com\"],\"phones\":[],\"detected_skills\":[\"cybersecurity\",\"linux\",\"network\",\"siem\",\"python\"]}', '2026-05-28 08:00:00', '2026-05-28 08:00:00', NULL),
(5, 'stu_nour_devops', 7, 'DevOps Cloud Junior', 'Ariana', 'Master Cloud Computing', 2, '[\"docker\",\"kubernetes\",\"linux\",\"ci/cd\",\"aws\"]', '[]', 'Master cloud computing. Projets Docker, Kubernetes, GitLab CI, deploiement AWS, supervision Prometheus et automatisation Linux.', '{\"emails\":[\"nour.haddad@example.com\"],\"phones\":[],\"detected_skills\":[\"docker\",\"kubernetes\",\"linux\",\"ci/cd\",\"aws\"]}', '2026-05-28 08:05:00', '2026-05-28 08:05:00', NULL),
(6, 'stu_mariem_flutter', 8, 'Developpeuse Mobile Flutter', 'Sousse', 'Licence Genie Logiciel', 1, '[\"flutter\",\"dart\",\"firebase\",\"mobile\",\"api rest\"]', '[]', 'Licence genie logiciel. Applications mobiles Flutter et Dart, authentification Firebase, consommation API REST, publication Android.', '{\"emails\":[\"mariem.benali@example.com\"],\"phones\":[],\"detected_skills\":[\"flutter\",\"dart\",\"firebase\",\"mobile\",\"api rest\"]}', '2026-05-28 08:10:00', '2026-05-28 08:10:00', NULL),
(7, 'stu_sami_bi', 9, 'Analyste BI Power BI', 'Tunis', 'Master Business Intelligence', 1, '[\"power bi\",\"sql\",\"etl\",\"data analysis\",\"excel\"]', '[]', 'Master BI. Tableaux de bord Power BI, modelisation etoile, SQL Server, ETL, Excel avance et indicateurs financiers.', '{\"emails\":[\"sami.jlassi@example.com\"],\"phones\":[],\"detected_skills\":[\"power bi\",\"sql\",\"etl\",\"data analysis\",\"excel\"]}', '2026-05-28 08:15:00', '2026-05-28 08:15:00', NULL),
(8, 'stu_lina_uiux', 10, 'UX UI Designer Junior', 'Nabeul', 'Licence Design Numerique', 1, '[\"figma\",\"ux\",\"ui\",\"user research\",\"prototyping\"]', '[]', 'Licence design numerique. Maquettes Figma, recherche utilisateur, wireframes, prototypes interactifs, tests utilisateurs et design system.', '{\"emails\":[\"lina.gharbi@example.com\"],\"phones\":[],\"detected_skills\":[\"figma\",\"ux\",\"ui\",\"user research\",\"prototyping\"]}', '2026-05-28 08:20:00', '2026-05-28 08:20:00', NULL),
(9, 'stu_karim_iot', 11, 'Ingenieur Embarque IoT', 'Sfax', 'Cycle Ingenieur Electronique', 2, '[\"c\",\"c++\",\"iot\",\"arduino\",\"embedded\"]', '[]', 'Cycle ingenieur electronique. Programmation C C++, cartes Arduino ESP32, capteurs IoT, MQTT, acquisition donnees et systemes embarques.', '{\"emails\":[\"karim.saidi@example.com\"],\"phones\":[],\"detected_skills\":[\"c\",\"c++\",\"iot\",\"arduino\",\"embedded\"]}', '2026-05-28 08:25:00', '2026-05-28 08:25:00', NULL),
(10, 'stu_rania_qa', 12, 'QA Automation Tester', 'Tunis', 'Licence Informatique', 1, '[\"selenium\",\"testing\",\"java\",\"api rest\",\"jira\"]', '[]', 'Licence informatique. Tests fonctionnels, Selenium WebDriver, tests API REST, Java, Jira, redaction de plans de test et rapports anomalies.', '{\"emails\":[\"rania.ktari@example.com\"],\"phones\":[],\"detected_skills\":[\"selenium\",\"testing\",\"java\",\"api rest\",\"jira\"]}', '2026-05-28 08:30:00', '2026-05-28 08:30:00', NULL),
(11, 'stu_oussama_sap', 13, 'Consultant SAP ABAP Junior', 'Tunis', 'Master Systeme Information', 1, '[\"sap\",\"abap\",\"sql\",\"erp\",\"business process\"]', '[]', 'Master systeme information. Introduction SAP ERP, developpement ABAP, SQL, processus achat vente et parametres fonctionnels.', '{\"emails\":[\"oussama.ferchichi@example.com\"],\"phones\":[],\"detected_skills\":[\"sap\",\"abap\",\"sql\",\"erp\",\"business process\"]}', '2026-05-28 08:35:00', '2026-05-28 08:35:00', NULL),
(12, 'stu_salma_marketing', 14, 'Assistante Marketing Digital SEO', 'Monastir', 'Licence Marketing Digital', 1, '[\"seo\",\"google analytics\",\"content marketing\",\"social media\",\"excel\"]', '[]', 'Licence marketing digital. SEO, Google Analytics, campagnes reseaux sociaux, calendrier editorial, reporting Excel et analyse trafic.', '{\"emails\":[\"salma.amri@example.com\"],\"phones\":[],\"detected_skills\":[\"seo\",\"google analytics\",\"content marketing\",\"social media\",\"excel\"]}', '2026-05-28 08:40:00', '2026-05-28 08:40:00', NULL),
(13, 'stu_mehdi_finance', 15, 'Analyste Finance Data', 'Tunis', 'Master Finance', 1, '[\"finance\",\"excel\",\"power bi\",\"sql\",\"data analysis\"]', '[]', 'Master finance. Modeles Excel, analyse tresorerie, reporting Power BI, SQL de base, controle de gestion et indicateurs financiers.', '{\"emails\":[\"mehdi.bouazizi@example.com\"],\"phones\":[],\"detected_skills\":[\"finance\",\"excel\",\"power bi\",\"sql\",\"data analysis\"]}', '2026-05-28 08:45:00', '2026-05-28 08:45:00', NULL),
(14, 'stu_hana_hris', 16, 'Assistante SIRH Data', 'Ariana', 'Licence Ressources Humaines', 1, '[\"hr\",\"excel\",\"data analysis\",\"power bi\",\"recruitment\"]', '[]', 'Licence RH. Suivi candidatures, reporting Excel, tableaux de bord RH Power BI, indicateurs recrutement et qualite donnees.', '{\"emails\":[\"hana.miled@example.com\"],\"phones\":[],\"detected_skills\":[\"hr\",\"excel\",\"data analysis\",\"power bi\",\"recruitment\"]}', '2026-05-28 08:50:00', '2026-05-28 08:50:00', NULL),
(15, 'stu_walid_laravel', 17, 'Developpeur PHP Laravel', 'Tunis', 'Licence Informatique', 2, '[\"php\",\"laravel\",\"mysql\",\"api rest\",\"git\"]', '[]', 'Licence informatique. Applications Laravel, MySQL, API REST, Blade, authentification, Git et integration de services tiers.', '{\"emails\":[\"walid.krichen@example.com\"],\"phones\":[],\"detected_skills\":[\"php\",\"laravel\",\"mysql\",\"api rest\",\"git\"]}', '2026-05-28 08:55:00', '2026-05-28 08:55:00', NULL),
(16, 'stu_aya_fastapi', 18, 'Developpeuse Python FastAPI', 'Sfax', 'Master Data Engineering', 1, '[\"python\",\"fastapi\",\"sql\",\"docker\",\"api rest\"]', '[]', 'Master data engineering. APIs Python FastAPI, SQL, Docker, tests unitaires, integration avec front-end et traitement de donnees.', '{\"emails\":[\"aya.rezgui@example.com\"],\"phones\":[],\"detected_skills\":[\"python\",\"fastapi\",\"sql\",\"docker\",\"api rest\"]}', '2026-05-28 09:00:00', '2026-05-28 09:00:00', NULL),
(17, 'stu_tarek_network', 19, 'Technicien Reseaux et Systemes', 'Bizerte', 'Licence Reseaux', 1, '[\"network\",\"linux\",\"windows server\",\"security\",\"helpdesk\"]', '[]', 'Licence reseaux. Administration Linux, Windows Server, routage, VLAN, support helpdesk, securite reseau et supervision.', '{\"emails\":[\"tarek.baccouche@example.com\"],\"phones\":[],\"detected_skills\":[\"network\",\"linux\",\"windows server\",\"security\",\"helpdesk\"]}', '2026-05-28 09:05:00', '2026-05-28 09:05:00', NULL),
(18, 'stu_cyrine_crm', 20, 'Consultante CRM Salesforce Junior', 'Tunis', 'Master Marketing et SI', 1, '[\"salesforce\",\"crm\",\"business analysis\",\"sql\",\"user support\"]', '[]', 'Master marketing et systemes information. CRM Salesforce, analyse besoin client, support utilisateurs, SQL de base et documentation fonctionnelle.', '{\"emails\":[\"cyrine.chaabane@example.com\"],\"phones\":[],\"detected_skills\":[\"salesforce\",\"crm\",\"business analysis\",\"sql\",\"user support\"]}', '2026-05-28 09:10:00', '2026-05-28 09:10:00', NULL),
(19, 'stu_bilel_unity', 21, 'Developpeur Unity Junior', 'Sousse', 'Licence Multimedia', 1, '[\"unity\",\"c#\",\"game development\",\"3d\",\"git\"]', '[]', 'Licence multimedia. Projets Unity 2D et 3D, scripts C#, interfaces jeu, optimisation scene, Git et prototypes interactifs.', '{\"emails\":[\"bilel.ayari@example.com\"],\"phones\":[],\"detected_skills\":[\"unity\",\"c#\",\"game development\",\"3d\",\"git\"]}', '2026-05-28 09:15:00', '2026-05-28 09:15:00', NULL),
(20, 'stu_farah_dataeng', 22, 'Data Engineer Junior', 'Tunis', 'Master Big Data', 2, '[\"python\",\"etl\",\"sql\",\"airflow\",\"data warehouse\"]', '[]', 'Master big data. Pipelines ETL, Python, SQL, Airflow, data warehouse, qualite donnees et automatisation de traitements.', '{\"emails\":[\"farah.toumi@example.com\"],\"phones\":[],\"detected_skills\":[\"python\",\"etl\",\"sql\",\"airflow\",\"data warehouse\"]}', '2026-05-28 09:20:00', '2026-05-28 09:20:00', NULL),
(21, 'stu_ahmed_android', 23, 'Developpeur Android Kotlin', 'Gabes', 'Licence Informatique Mobile', 1, '[\"android\",\"kotlin\",\"mobile\",\"api rest\",\"firebase\"]', '[]', 'Licence informatique mobile. Applications Android Kotlin, Firebase, architecture MVVM, API REST et publication interne.', '{\"emails\":[\"ahmed.laabidi@example.com\"],\"phones\":[],\"detected_skills\":[\"android\",\"kotlin\",\"mobile\",\"api rest\",\"firebase\"]}', '2026-05-28 09:25:00', '2026-05-28 09:25:00', NULL),
(22, 'stu_rym_product', 24, 'Product Owner Junior Agile', 'Tunis', 'Master Management SI', 1, '[\"agile\",\"scrum\",\"product management\",\"jira\",\"user stories\"]', '[]', 'Master management des systemes information. Scrum, backlog produit, user stories, Jira, ateliers metier et priorisation.', '{\"emails\":[\"rym.kallel@example.com\"],\"phones\":[],\"detected_skills\":[\"agile\",\"scrum\",\"product management\",\"jira\",\"user stories\"]}', '2026-05-28 09:30:00', '2026-05-28 09:30:00', NULL),
(23, 'stu_omar_blockchain', 25, 'Developpeur Blockchain Solidity', 'Tunis', 'Cycle Ingenieur Informatique', 1, '[\"solidity\",\"blockchain\",\"javascript\",\"web3\",\"security\"]', '[]', 'Cycle ingenieur informatique. Smart contracts Solidity, Web3, JavaScript, tests de contrats, securite blockchain et dApps.', '{\"emails\":[\"omar.cherif@example.com\"],\"phones\":[],\"detected_skills\":[\"solidity\",\"blockchain\",\"javascript\",\"web3\",\"security\"]}', '2026-05-28 09:35:00', '2026-05-28 09:35:00', NULL),
(32, 'stu_uzubtait', 36, 'Ingénieur en génie informatique', 'Sfax, Tunisie', 'Ingénieur en génie informatique', 0, '[\"php\",\"laravel\",\"python\",\"mysql\",\"java\",\"javascript\",\"typescript\",\"vue\",\"angular\",\"sql\",\"cloud\",\"mobile\"]', '[]', '09/03/1989 \r\nBac + 7 \r\n\r\n207 Avenue Cherif EDRISSI 3021 Sakiet-Ezzit Sfax \r\nTel : 216 23 373 920 \r\nMail : hammami.houssem@gmail.com \r\n\r\n      COMPETENCES CLES \r\nMaîtrise des logiciels & frameworks : \r\n-Traitement d’image : MATLAB, Adobe (Photoshop, Illustrateur \r\net Fireworks) \r\n- Conception & Animation : Adobe Flash Professional et 3Ds \r\nMax. \r\n- Montage vidéo & son : AudaCity, Movie maker et Adobe \r\n(Premiere Pro, Soundbooth et After effect). \r\n- Outils de Développement : Visual Studio, Devexpress, Eclipse, \r\nEmule 8086, Andoid SDK, Adobe Dreamweaver, Flash Builder \r\n- Outils de Modélisation & Conception : ArgoUML, StarUML, \r\nAMC Designer, Pacestar UML Diagrammer, Eclipse(MDA) \r\n- Serveurs Web: Wamp server, Node, NPM, Composer, \r\nFileZella, easy PHP, Apache Tomcat, Oracle et JBOSS. \r\n- Framework & CMS : VueJs, Angular, Ionic, Android, \r\nCodeigniter, Laravel, Prestashop, Joomla, WordPress, magento. \r\n\r\n     FORMATION \r\n\r\n2013-2015 : Ingénierie en Informatique : \r\nGénie Logiciel et Informatique \r\nDécisionnelle, IIT de Sfax \r\n\r\nHoussem HAMMAMI \r\n09/03/1989 \r\nIngénieur en Informatique \r\nGénie logiciel \r\n\r\nLangages de programmation :  \r\nPascal, HTML, CSS, PHP, JavaScript, JQuery, JSON, \r\nAjax, TypeScript, C, C#, XML, Java, AS3, J2EE, Flex, \r\nMATLAB \r\n\r\nBase de données : \r\nMangoDB, MYSQL, ACCESS, SQL Server \r\n\r\nLangages et méthodes de conception : \r\nMerise, UML, MDA \r\n\r\nLangues :  \r\nArabe : langue maternelle \r\nFrançais : bon niveau (lu, parler, écrit) \r\nAnglais : bon niveau (lu, parler, écrit) \r\nItalien : moyen (formation option 2 ans) \r\n\r\nSpécialité : \r\nGénie Logiciel \r\nOptimisation \r\nCloud Computing \r\nSolutions Web \r\n\r\nMémoire : \r\nMise en place d’une solution \r\nen ligne pour la saisie des \r\nnotes avec le module de \r\ncompostage \r\n\r\n2011-2013 : Mastère Professionnel en \r\nIngénierie de Multimédia IM, ISIM de \r\nSfax \r\n\r\nSpécialité : \r\nProg. Orienté Objet  \r\nProg Orientée Service \r\nConception Assistée \r\npar Ordinateur \r\n\r\nMémoire : \r\nOutil pour l’aide à la \r\nvalidation temporelle et \r\nspatiale de documents \r\nSMIL \r\n\r\n2008-2011 : Licence appliquée en \r\nInformatique : Technologies Multimédia \r\n& Web, ISIM de Sfax \r\n\r\nSpécialité : \r\nAlgorithme et prog. \r\nModélisation 3D \r\nAnimation 2D \r\nCréation des sites web \r\n\r\nMémoire : \r\nApplication de gestion d’un \r\nrestaurant. \r\n\r\n2007-2008 : BAC Informatique, Mongi \r\nSlim de Sfax \r\n\r\nStage : \r\nCréation d’une application de gestion commerciale \r\n\r\n      EXPERIENCES PROFESSIONNELLES \r\n04/04/2016 : Gérant & Responsable Technique : Best Solutions : Fondation de la société Best Solutions. \r\n15/06/2015 : Ingénieur : NATECH : Mise en place d’un service web pour la société. \r\n01/03/2015 : PFE : SIFAST : Mise en place d’une solution en ligne pour la saisie des notes avec le module de compostage. \r\n23/06/2014 : PFA : Mise en place d’une solution Cloud Computing IAAS. (OpenStack IceHouse, CentOs. RDO, VMWare). \r\n07, 08, 09/03/2014 : Participation à StartupWeekend Sfax #3 à l\'ENIS avec le projet Face Recognizer. \r\n01, 02/03/2014 : Participation à l’évènement international Droidcon Tunisia 2014 à La Medina, Yasmine Hammamet. \r\n23/11/2013 : Participation à la formation We are the world organisé par Mediter. Com. for Mob. Dev.de 9 à 16 à l\'ISIMS. \r\n26, 27/10/2013 : Participation au challenge Dev Camp 2013 à l’ISET SFAX, (Jeux pour windows phone 8 « SmartChild »). \r\n01/03/2013 : PFE : Création d’un outil pour l’aide à la validation temporelle et spatiale de documents SMIL (C#, SMIL). \r\n01/03/2011 : PFE : e-media : Création d’une application de gestion d’un restaurant. (UML, C#, Devexpress,SQL Server). \r\n15/06/2010 : Stage d’été : e-media : Création d\'une application de gestion commerciale (C# et Devexpress).', '{\"emails\":[\"hammami.houssem@gmail.com\"],\"phones\":[\"216 23 373 920\"],\"detected_skills\":[\"java\",\"php\",\"javascript\",\"typescript\",\"vue\",\"angular\",\"sql\",\"cloud\",\"mobile\"]}', '2026-08-20 09:26:33', '2026-08-20 10:08:21', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `sr_users`
--

CREATE TABLE `sr_users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `public_id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role` enum('student','recruiter','admin') NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp(6) NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `sr_users`
--

INSERT INTO `sr_users` (`id`, `public_id`, `name`, `email`, `role`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'usr_admin', 'Admin Smart-Recruit', 'admin@smart-recruit.test', 'admin', NULL, '$2y$10$Ot8PVnbAsyoTVrx2QhQcsO21Mgfm/EmqN7phbC/y36tkJ7dUR6QSG', NULL, '2026-06-17 22:39:41', '2026-08-19 10:28:27', NULL),
(2, 'usr_recruiter', 'Recruteur Smart-Recruit', 'recruteur@smart-recruit.test', 'recruiter', NULL, '$2y$10$Ot8PVnbAsyoTVrx2QhQcsO21Mgfm/EmqN7phbC/y36tkJ7dUR6QSG', NULL, '2026-06-17 22:39:41', '2026-09-11 11:06:45', NULL),
(3, 'usr_student', 'Amira Ben Salem', 'amira.bensalem@example.com', 'student', NULL, '$2y$10$Ot8PVnbAsyoTVrx2QhQcsO21Mgfm/EmqN7phbC/y36tkJ7dUR6QSG', NULL, '2026-06-17 22:39:41', '2026-09-11 14:35:43', NULL),
(4, 'usr_yassine', 'Yassine Trabelsi', 'yassine.trabelsi@example.com', 'student', NULL, '$2y$10$Ot8PVnbAsyoTVrx2QhQcsO21Mgfm/EmqN7phbC/y36tkJ7dUR6QSG', NULL, '2026-05-27 08:10:00', '2026-08-19 10:28:27', NULL),
(5, 'usr_ines', 'Ines Kammoun', 'ines.kammoun@example.com', 'student', NULL, '$2y$10$Ot8PVnbAsyoTVrx2QhQcsO21Mgfm/EmqN7phbC/y36tkJ7dUR6QSG', NULL, '2026-05-27 08:20:00', '2026-08-19 10:28:27', NULL),
(6, 'usr_selim_cyber', 'Selim Mansouri', 'selim.mansouri@example.com', 'student', NULL, '$2y$10$Ot8PVnbAsyoTVrx2QhQcsO21Mgfm/EmqN7phbC/y36tkJ7dUR6QSG', NULL, '2026-05-28 08:00:00', '2026-08-19 10:28:27', NULL),
(7, 'usr_nour_devops', 'Nour Haddad', 'nour.haddad@example.com', 'student', NULL, '$2y$10$Ot8PVnbAsyoTVrx2QhQcsO21Mgfm/EmqN7phbC/y36tkJ7dUR6QSG', NULL, '2026-05-28 08:05:00', '2026-08-19 10:28:27', NULL),
(8, 'usr_mariem_flutter', 'Mariem Ben Ali', 'mariem.benali@example.com', 'student', NULL, '$2y$10$Ot8PVnbAsyoTVrx2QhQcsO21Mgfm/EmqN7phbC/y36tkJ7dUR6QSG', NULL, '2026-05-28 08:10:00', '2026-08-19 10:28:27', NULL),
(9, 'usr_sami_bi', 'Sami Jlassi', 'sami.jlassi@example.com', 'student', NULL, '$2y$10$Ot8PVnbAsyoTVrx2QhQcsO21Mgfm/EmqN7phbC/y36tkJ7dUR6QSG', NULL, '2026-05-28 08:15:00', '2026-08-19 10:28:27', NULL),
(10, 'usr_lina_uiux', 'Lina Gharbi', 'lina.gharbi@example.com', 'student', NULL, '$2y$10$Ot8PVnbAsyoTVrx2QhQcsO21Mgfm/EmqN7phbC/y36tkJ7dUR6QSG', NULL, '2026-05-28 08:20:00', '2026-08-19 10:28:27', NULL),
(11, 'usr_karim_iot', 'Karim Saidi', 'karim.saidi@example.com', 'student', NULL, '$2y$10$Ot8PVnbAsyoTVrx2QhQcsO21Mgfm/EmqN7phbC/y36tkJ7dUR6QSG', NULL, '2026-05-28 08:25:00', '2026-08-19 10:28:27', NULL),
(12, 'usr_rania_qa', 'Rania Ktari', 'rania.ktari@example.com', 'student', NULL, '$2y$10$Ot8PVnbAsyoTVrx2QhQcsO21Mgfm/EmqN7phbC/y36tkJ7dUR6QSG', NULL, '2026-05-28 08:30:00', '2026-08-19 10:28:27', NULL),
(13, 'usr_oussama_sap', 'Oussama Ferchichi', 'oussama.ferchichi@example.com', 'student', NULL, '$2y$10$Ot8PVnbAsyoTVrx2QhQcsO21Mgfm/EmqN7phbC/y36tkJ7dUR6QSG', NULL, '2026-05-28 08:35:00', '2026-08-19 10:28:27', NULL),
(14, 'usr_salma_marketing', 'Salma Amri', 'salma.amri@example.com', 'student', NULL, '$2y$10$Ot8PVnbAsyoTVrx2QhQcsO21Mgfm/EmqN7phbC/y36tkJ7dUR6QSG', NULL, '2026-05-28 08:40:00', '2026-08-19 10:28:27', NULL),
(15, 'usr_mehdi_finance', 'Mehdi Bouazizi', 'mehdi.bouazizi@example.com', 'student', NULL, '$2y$10$Ot8PVnbAsyoTVrx2QhQcsO21Mgfm/EmqN7phbC/y36tkJ7dUR6QSG', NULL, '2026-05-28 08:45:00', '2026-08-19 10:28:27', NULL),
(16, 'usr_hana_hris', 'Hana Miled', 'hana.miled@example.com', 'student', NULL, '$2y$10$Ot8PVnbAsyoTVrx2QhQcsO21Mgfm/EmqN7phbC/y36tkJ7dUR6QSG', NULL, '2026-05-28 08:50:00', '2026-08-19 10:28:27', NULL),
(17, 'usr_walid_laravel', 'Walid Krichen', 'walid.krichen@example.com', 'student', NULL, '$2y$10$Ot8PVnbAsyoTVrx2QhQcsO21Mgfm/EmqN7phbC/y36tkJ7dUR6QSG', NULL, '2026-05-28 08:55:00', '2026-08-19 10:28:27', NULL),
(18, 'usr_aya_fastapi', 'Aya Rezgui', 'aya.rezgui@example.com', 'student', NULL, '$2y$10$Ot8PVnbAsyoTVrx2QhQcsO21Mgfm/EmqN7phbC/y36tkJ7dUR6QSG', NULL, '2026-05-28 09:00:00', '2026-08-19 10:28:27', NULL),
(19, 'usr_tarek_network', 'Tarek Baccouche', 'tarek.baccouche@example.com', 'student', NULL, '$2y$10$Ot8PVnbAsyoTVrx2QhQcsO21Mgfm/EmqN7phbC/y36tkJ7dUR6QSG', NULL, '2026-05-28 09:05:00', '2026-08-19 10:28:27', NULL),
(20, 'usr_cyrine_crm', 'Cyrine Chaabane', 'cyrine.chaabane@example.com', 'student', NULL, '$2y$10$Ot8PVnbAsyoTVrx2QhQcsO21Mgfm/EmqN7phbC/y36tkJ7dUR6QSG', NULL, '2026-05-28 09:10:00', '2026-08-19 10:28:27', NULL),
(21, 'usr_bilel_unity', 'Bilel Ayari', 'bilel.ayari@example.com', 'student', NULL, '$2y$10$Ot8PVnbAsyoTVrx2QhQcsO21Mgfm/EmqN7phbC/y36tkJ7dUR6QSG', NULL, '2026-05-28 09:15:00', '2026-08-19 10:28:27', NULL),
(22, 'usr_farah_dataeng', 'Farah Toumi', 'farah.toumi@example.com', 'student', NULL, '$2y$10$Ot8PVnbAsyoTVrx2QhQcsO21Mgfm/EmqN7phbC/y36tkJ7dUR6QSG', NULL, '2026-05-28 09:20:00', '2026-08-19 10:28:27', NULL),
(23, 'usr_ahmed_android', 'Ahmed Laabidi', 'ahmed.laabidi@example.com', 'student', NULL, '$2y$10$Ot8PVnbAsyoTVrx2QhQcsO21Mgfm/EmqN7phbC/y36tkJ7dUR6QSG', NULL, '2026-05-28 09:25:00', '2026-08-19 10:28:27', NULL),
(24, 'usr_rym_product', 'Rym Kallel', 'rym.kallel@example.com', 'student', NULL, '$2y$10$Ot8PVnbAsyoTVrx2QhQcsO21Mgfm/EmqN7phbC/y36tkJ7dUR6QSG', NULL, '2026-05-28 09:30:00', '2026-08-19 10:28:27', NULL),
(25, 'usr_omar_blockchain', 'Omar Cherif', 'omar.cherif@example.com', 'student', NULL, '$2y$10$Ot8PVnbAsyoTVrx2QhQcsO21Mgfm/EmqN7phbC/y36tkJ7dUR6QSG', NULL, '2026-05-28 09:35:00', '2026-08-19 10:28:27', NULL),
(36, 'usr_vkdxs7pr', 'Houssem Hammami', 'hammami.houssem@gmail.com', 'student', NULL, '$2y$10$InKS9J7oVKBA8o0OaeDvxu0tsvEh9gxF3EmslfqNelrAwQu54D7pS', NULL, '2026-08-20 09:26:33', '2026-08-20 10:08:21', NULL);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `sr_applications`
--
ALTER TABLE `sr_applications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `public_id` (`public_id`),
  ADD UNIQUE KEY `sr_applications_offer_student_unique` (`offer_id`,`student_profile_id`),
  ADD KEY `sr_applications_student_profile_id_foreign` (`student_profile_id`),
  ADD KEY `sr_applications_deleted_at_index` (`deleted_at`);

--
-- Index pour la table `sr_cv_documents`
--
ALTER TABLE `sr_cv_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sr_cv_documents_student_profile_id_foreign` (`student_profile_id`);

--
-- Index pour la table `sr_match_scores`
--
ALTER TABLE `sr_match_scores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sr_match_scores_score_index` (`score`),
  ADD KEY `sr_match_scores_application_id_foreign` (`application_id`);

--
-- Index pour la table `sr_offers`
--
ALTER TABLE `sr_offers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `public_id` (`public_id`),
  ADD KEY `sr_offers_status_created_at_index` (`status`,`created_at`),
  ADD KEY `sr_offers_recruiter_profile_id_foreign` (`recruiter_profile_id`),
  ADD KEY `sr_offers_deleted_at_index` (`deleted_at`);

--
-- Index pour la table `sr_recruiter_profiles`
--
ALTER TABLE `sr_recruiter_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `public_id` (`public_id`),
  ADD KEY `sr_recruiter_profiles_user_id_foreign` (`user_id`),
  ADD KEY `sr_recruiter_profiles_deleted_at_index` (`deleted_at`);

--
-- Index pour la table `sr_student_profiles`
--
ALTER TABLE `sr_student_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `public_id` (`public_id`),
  ADD KEY `sr_student_profiles_user_id_foreign` (`user_id`),
  ADD KEY `sr_student_profiles_deleted_at_index` (`deleted_at`);

--
-- Index pour la table `sr_users`
--
ALTER TABLE `sr_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `public_id` (`public_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `sr_users_deleted_at_index` (`deleted_at`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `sr_applications`
--
ALTER TABLE `sr_applications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT pour la table `sr_cv_documents`
--
ALTER TABLE `sr_cv_documents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT pour la table `sr_match_scores`
--
ALTER TABLE `sr_match_scores`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT pour la table `sr_offers`
--
ALTER TABLE `sr_offers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT pour la table `sr_recruiter_profiles`
--
ALTER TABLE `sr_recruiter_profiles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `sr_student_profiles`
--
ALTER TABLE `sr_student_profiles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT pour la table `sr_users`
--
ALTER TABLE `sr_users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `sr_applications`
--
ALTER TABLE `sr_applications`
  ADD CONSTRAINT `sr_applications_offer_id_foreign` FOREIGN KEY (`offer_id`) REFERENCES `sr_offers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sr_applications_student_profile_id_foreign` FOREIGN KEY (`student_profile_id`) REFERENCES `sr_student_profiles` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `sr_cv_documents`
--
ALTER TABLE `sr_cv_documents`
  ADD CONSTRAINT `sr_cv_documents_student_profile_id_foreign` FOREIGN KEY (`student_profile_id`) REFERENCES `sr_student_profiles` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `sr_match_scores`
--
ALTER TABLE `sr_match_scores`
  ADD CONSTRAINT `sr_match_scores_application_id_foreign` FOREIGN KEY (`application_id`) REFERENCES `sr_applications` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `sr_offers`
--
ALTER TABLE `sr_offers`
  ADD CONSTRAINT `sr_offers_recruiter_profile_id_foreign` FOREIGN KEY (`recruiter_profile_id`) REFERENCES `sr_recruiter_profiles` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `sr_recruiter_profiles`
--
ALTER TABLE `sr_recruiter_profiles`
  ADD CONSTRAINT `sr_recruiter_profiles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `sr_users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `sr_student_profiles`
--
ALTER TABLE `sr_student_profiles`
  ADD CONSTRAINT `sr_student_profiles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `sr_users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
