<?php $__env->startSection('title', 'Modifier le profil'); ?>

<?php $__env->startSection('content'); ?>
<section class="form-shell wide">
    <div class="section-title">
        <h1>Modifier le profil</h1>
        <a href="<?php echo e(route('students.show', $student['id'])); ?>">Retour au profil</a>
    </div>
    <p class="muted">Mettez à jour le parcours et les compétences, puis vérifiez les informations du CV.</p>

    <form class="form-card" method="post" action="<?php echo e(route('students.update', $student['id'])); ?>" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <div class="form-section">
            <h2>Informations et parcours</h2>
            <div class="grid-2">
                <label>
                    Nom complet
                    <input name="name" value="<?php echo e(old('name', $student['name'])); ?>" required>
                </label>
                <label>
                    Adresse e-mail
                    <input name="email" type="email" value="<?php echo e(old('email', $student['email'])); ?>" required>
                </label>
                <label>
                    Titre du profil
                    <input name="headline" value="<?php echo e(old('headline', $student['headline'])); ?>" required>
                </label>
                <label>
                    Localisation
                    <input name="location" value="<?php echo e(old('location', $student['location'])); ?>">
                </label>
                <label>
                    Diplôme
                    <input name="education" value="<?php echo e(old('education', $student['education'])); ?>">
                </label>
                <label>
                    Années d'expérience
                    <input name="experience_years" type="number" min="0" max="60" value="<?php echo e(old('experience_years', $student['experience_years'])); ?>">
                </label>
            </div>
            <p class="field-help">Vérifiez la formation et la durée des expériences professionnelles présentées dans le profil.</p>
        </div>

        <div class="form-section">
            <h2>Compétences</h2>
            <label>
                Compétences du profil
                <input name="skills" value="<?php echo e(old('skills', implode(', ', $student['skills']))); ?>" placeholder="java, react, sql">
            </label>
            <p class="field-help">Séparez les compétences par des virgules et gardez celles qui correspondent au parcours.</p>
        </div>

        <div class="form-section">
            <h2>Liens professionnels</h2>
            <div class="grid-2">
                <label>
                    GitHub
                    <input name="github" type="url" value="<?php echo e(old('github', $student['links']['github'] ?? '')); ?>">
                </label>
                <label>
                    LinkedIn
                    <input name="linkedin" type="url" value="<?php echo e(old('linkedin', $student['links']['linkedin'] ?? '')); ?>">
                </label>
            </div>
            <label>
                Portfolio
                <input name="portfolio" type="url" value="<?php echo e(old('portfolio', $student['links']['portfolio'] ?? '')); ?>">
            </label>
            <p class="field-help">Ajoutez les adresses complètes des pages que vous souhaitez partager.</p>
        </div>

        <div class="form-section">
            <h2>CV</h2>
            <label>
                Fichier du CV
                <input name="cv_file" type="file" accept=".pdf,.txt,.md">
            </label>
            <p class="field-help">Formats acceptés : PDF, texte (.txt) et Markdown (.md).</p>
            <?php if($student['file_name']): ?>
                <p class="field-help">Fichier actuel : <?php echo e($student['file_name']); ?>. Envoyez un nouveau fichier pour le remplacer.</p>
            <?php endif; ?>

            <label>
                Contenu du CV
                <textarea name="cv_text" rows="8"><?php echo e(old('cv_text', $student['cv_text'])); ?></textarea>
            </label>
            <p class="field-help">Relisez le contenu et corrigez les informations manquantes ou mal extraites.</p>
        </div>

        <div class="form-section">
            <h2>Coordonnées détectées</h2>
            <div class="grid-2">
                <label>
                    Adresses e-mail
                    <input name="emails" value="<?php echo e(old('emails', implode(', ', $student['metadata']['emails'] ?? []))); ?>" placeholder="e-mails séparés par des virgules">
                </label>
                <label>
                    Numéros de téléphone
                    <input name="phones" value="<?php echo e(old('phones', implode(', ', $student['metadata']['phones'] ?? []))); ?>" placeholder="téléphones séparés par des virgules">
                </label>
            </div>
            <p class="field-help">Ces coordonnées proviennent du CV. Corrigez-les si besoin et séparez plusieurs valeurs par des virgules.</p>
        </div>

        <button class="button" type="submit">Enregistrer les modifications</button>
    </form>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\smart-recruit\resources\views/students/edit.blade.php ENDPATH**/ ?>