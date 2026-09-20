<?php $__env->startSection('title', 'Créer une offre'); ?>

<?php $__env->startSection('content'); ?>
<section class="form-shell wide">
    <div class="section-title">
        <h1>Créer une offre de stage</h1>
        <a href="<?php echo e(route('offers.index')); ?>">Retour aux offres</a>
    </div>
    <p class="muted">Présentez votre mission et les compétences que vous recherchez.</p>

    <form class="form-card" method="post" action="<?php echo e(route('offers.store')); ?>">
        <?php echo csrf_field(); ?>
        <div class="form-section">
            <h2>Informations sur l'offre</h2>
            <div class="grid-2">
                <label>
                    Titre de l'offre
                    <input name="title" value="<?php echo e(old('title')); ?>" required>
                </label>
                <label>
                    Entreprise
                    <input name="company" value="<?php echo e(old('company')); ?>" required>
                </label>
                <label>
                    Localisation
                    <input name="location" value="<?php echo e(old('location')); ?>">
                </label>
                <label>
                    Type d'opportunité
                    <input name="type" value="<?php echo e(old('type', 'Stage PFE')); ?>">
                </label>
            </div>
        </div>

        <div class="form-section">
            <h2>Compétences recherchées</h2>
            <label>
                Compétences requises
                <input name="required_skills" value="<?php echo e(old('required_skills')); ?>" placeholder="python, nlp, machine learning">
            </label>
            <p class="field-help">Séparez les compétences par des virgules pour préciser les besoins de votre mission.</p>
        </div>

        <div class="form-section">
            <h2>La mission</h2>
            <label>
                Description de l'offre
                <textarea name="description" rows="10" required><?php echo e(old('description')); ?></textarea>
            </label>
            <p class="field-help">Décrivez les missions, le contexte du projet et le profil attendu.</p>
        </div>

        <button class="button" type="submit">Publier l'offre</button>
    </form>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH H:\smart-recruit\resources\views/offers/create.blade.php ENDPATH**/ ?>