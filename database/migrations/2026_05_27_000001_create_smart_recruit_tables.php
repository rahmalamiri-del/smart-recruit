<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('sr_users')) {
            Schema::create('sr_users', function (Blueprint $table): void {
                $table->id();
                $table->string('public_id')->unique();
                $table->string('name');
                $table->string('email')->unique();
                $table->enum('role', ['student', 'recruiter', 'admin']);
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->rememberToken();
                $table->timestamps();
            });
        } else {
            $this->ensurePublicId('sr_users', 'usr');
        }

        if (! Schema::hasTable('sr_student_profiles')) {
            Schema::create('sr_student_profiles', function (Blueprint $table): void {
                $table->id();
                $table->string('public_id')->unique();
                $table->foreignId('user_id')->constrained('sr_users')->cascadeOnDelete();
                $table->string('headline')->nullable();
                $table->string('location')->nullable();
                $table->string('education')->nullable();
                $table->unsignedTinyInteger('experience_years')->default(0);
                $table->json('skills')->nullable();
                $table->json('links')->nullable();
                $table->longText('cv_text')->nullable();
                $table->json('cv_metadata')->nullable();
                $table->timestamps();
            });
        } else {
            $this->ensurePublicId('sr_student_profiles', 'stu');
        }

        if (! Schema::hasTable('sr_recruiter_profiles')) {
            Schema::create('sr_recruiter_profiles', function (Blueprint $table): void {
                $table->id();
                $table->string('public_id')->unique();
                $table->foreignId('user_id')->constrained('sr_users')->cascadeOnDelete();
                $table->string('company_name');
                $table->string('position')->nullable();
                $table->string('website')->nullable();
                $table->timestamps();
            });
        } else {
            $this->ensurePublicId('sr_recruiter_profiles', 'rec');
        }

        if (! Schema::hasTable('sr_cv_documents')) {
            Schema::create('sr_cv_documents', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('student_profile_id')->constrained('sr_student_profiles')->cascadeOnDelete();
                $table->string('original_name');
                $table->string('stored_path');
                $table->string('mime_type')->nullable();
                $table->unsignedInteger('size')->default(0);
                $table->json('parser_result')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('sr_offers')) {
            Schema::create('sr_offers', function (Blueprint $table): void {
                $table->id();
                $table->string('public_id')->unique();
                $table->foreignId('recruiter_profile_id')->nullable()->constrained('sr_recruiter_profiles')->nullOnDelete();
                $table->string('title');
                $table->string('company');
                $table->string('location')->nullable();
                $table->string('type')->default('Stage');
                $table->longText('description');
                $table->json('required_skills')->nullable();
                $table->enum('status', ['draft', 'published', 'closed'])->default('published');
                $table->timestamps();
                $table->index(['status', 'created_at']);
            });
        } else {
            $this->ensurePublicId('sr_offers', 'off');
        }

        if (! Schema::hasTable('sr_applications')) {
            Schema::create('sr_applications', function (Blueprint $table): void {
                $table->id();
                $table->string('public_id')->unique();
                $table->foreignId('offer_id')->constrained('sr_offers')->cascadeOnDelete();
                $table->foreignId('student_profile_id')->constrained('sr_student_profiles')->cascadeOnDelete();
                $table->enum('status', ['submitted', 'shortlisted', 'rejected', 'interview'])->default('submitted');
                $table->timestamp('applied_at')->useCurrent();
                $table->timestamps();
                $table->unique(['offer_id', 'student_profile_id']);
            });
        } else {
            $this->ensurePublicId('sr_applications', 'app');
        }

        if (! Schema::hasTable('sr_match_scores')) {
            Schema::create('sr_match_scores', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('application_id')->constrained('sr_applications')->cascadeOnDelete();
                $table->unsignedTinyInteger('score');
                $table->unsignedTinyInteger('text_similarity')->default(0);
                $table->unsignedTinyInteger('semantic_coverage')->default(0);
                $table->json('matched_skills')->nullable();
                $table->json('missing_skills')->nullable();
                $table->json('raw_response')->nullable();
                $table->timestamps();
                $table->index('score');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('sr_match_scores');
        Schema::dropIfExists('sr_applications');
        Schema::dropIfExists('sr_offers');
        Schema::dropIfExists('sr_cv_documents');
        Schema::dropIfExists('sr_recruiter_profiles');
        Schema::dropIfExists('sr_student_profiles');
        Schema::dropIfExists('sr_users');
    }

    private function ensurePublicId(string $table, string $prefix): void
    {
        if (! Schema::hasColumn($table, 'public_id')) {
            Schema::table($table, function (Blueprint $table): void {
                $table->string('public_id')->nullable()->after('id');
            });
        }

        DB::table($table)
            ->whereNull('public_id')
            ->orderBy('id')
            ->select('id')
            ->get()
            ->each(function (object $row) use ($table, $prefix): void {
                DB::table($table)
                    ->where('id', $row->id)
                    ->update(['public_id' => $prefix.'_'.$row->id]);
            });
    }
};
