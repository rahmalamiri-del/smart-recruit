<?php $__env->startSection('title', 'Ajouter un profil candidat'); ?>

<?php $__env->startSection('content'); ?>
<section class="form-shell wide">
    <div class="section-title">
        <h1>Ajouter un profil candidat</h1>
        <a href="<?php echo e(route('students.index')); ?>">Retour aux profils</a>
    </div>
    <p class="muted">Rassemblez les informations du parcours, les compétences et le CV dans un même profil.</p>

    <form class="form-card" method="post" action="<?php echo e(route('students.store')); ?>" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <div class="form-section">
            <h2>Informations et parcours</h2>
            <div class="grid-2">
                <label>
                    Nom complet
                    <input name="name" value="<?php echo e(old('name')); ?>" required>
                </label>
                <label>
                    Adresse e-mail
                    <input name="email" type="email" value="<?php echo e(old('email')); ?>" required>
                </label>
                <label>
                    Titre du profil
                    <input name="headline" value="<?php echo e(old('headline')); ?>" required>
                </label>
                <label>
                    Localisation
                    <input name="location" value="<?php echo e(old('location')); ?>">
                </label>
                <label>
                    Diplôme
                    <input name="education" value="<?php echo e(old('education')); ?>">
                </label>
                <label>
                    Années d'expérience
                    <input name="experience_years" type="number" min="0" max="60" value="<?php echo e(old('experience_years')); ?>" placeholder="laisser vide pour détection automatique via le CV">
                </label>
            </div>
            <p class="field-help">Un titre précis permet de présenter le domaine de formation ou le métier recherché.</p>
        </div>

        <div class="form-section">
            <h2>Compétences</h2>
            <label>
                Compétences du profil
                <input name="skills" value="<?php echo e(old('skills')); ?>" placeholder="java, react, sql">
            </label>
            <p class="field-help">Séparez les compétences par des virgules. Vous pouvez compléter celles présentes dans le CV.</p>
        </div>

        <div class="form-section">
            <h2>Liens professionnels</h2>
            <div class="grid-2">
                <label>
                    GitHub
                    <input name="github" type="url" value="<?php echo e(old('github')); ?>">
                </label>
                <label>
                    LinkedIn
                    <input name="linkedin" type="url" value="<?php echo e(old('linkedin')); ?>">
                </label>
            </div>
            <label>
                Portfolio
                <input name="portfolio" type="url" value="<?php echo e(old('portfolio')); ?>">
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
            <label>
                Contenu du CV
                <textarea name="cv_text" rows="8"><?php echo e(old('cv_text')); ?></textarea>
            </label>
            <p class="field-help">Collez le contenu du CV ou ajoutez des précisions pour compléter le document.</p>
        </div>

        <button class="button" type="submit">Créer le profil</button>
    </form>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH H:\smart-recruit\resources\views/students/create.blade.php ENDPATH**/ ?>