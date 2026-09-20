<?php $__env->startSection('title', 'Modifier l\'offre'); ?>

<?php $__env->startSection('content'); ?>
<section class="form-shell wide">
    <div class="section-title">
        <h1>Modifier l'offre de stage</h1>
        <a href="<?php echo e(route('offers.show', $offer['id'])); ?>">Retour à l'offre</a>
    </div>
    <p class="muted">Actualisez les informations de votre offre et son état de publication.</p>

    <form class="form-card" method="post" action="<?php echo e(route('offers.update', $offer['id'])); ?>">
        <?php echo csrf_field(); ?>
        <div class="form-section">
            <h2>Informations sur l'offre</h2>
            <div class="grid-2">
                <label>
                    Titre de l'offre
                    <input name="title" value="<?php echo e(old('title', $offer['title'])); ?>" required>
                </label>
                <label>
                    Entreprise
                    <input name="company" value="<?php echo e(old('company', $offer['company'])); ?>" required>
                </label>
                <label>
                    Localisation
                    <input name="location" value="<?php echo e(old('location', $offer['location'])); ?>">
                </label>
                <label>
                    Type d'opportunité
                    <input name="type" value="<?php echo e(old('type', $offer['type'])); ?>">
                </label>
                <label>
                    État de publication
                    <select name="status">
                        <?php $__currentLoopData = ['published' => 'Publiée', 'draft' => 'Brouillon', 'closed' => 'Fermée']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($value); ?>" <?php if(old('status', $offer['status']) === $value): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </label>
            </div>
        </div>

        <div class="form-section">
            <h2>Compétences recherchées</h2>
            <label>
                Compétences requises
                <input name="required_skills" value="<?php echo e(old('required_skills', implode(', ', $offer['required_skills']))); ?>" placeholder="python, nlp, machine learning">
            </label>
            <p class="field-help">Séparez les compétences par des virgules pour préciser les besoins de votre mission.</p>
        </div>

        <div class="form-section">
            <h2>La mission</h2>
            <label>
                Description de l'offre
                <textarea name="description" rows="10" required><?php echo e(old('description', $offer['description'])); ?></textarea>
            </label>
            <p class="field-help">Décrivez les missions, le contexte du projet et le profil attendu.</p>
        </div>

        <button class="button" type="submit">Enregistrer les modifications</button>
    </form>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\smart-recruit\resources\views/offers/edit.blade.php ENDPATH**/ ?>