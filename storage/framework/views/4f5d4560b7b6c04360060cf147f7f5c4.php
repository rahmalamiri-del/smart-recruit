<?php $__env->startSection('title', $account ? 'Modifier le compte' : 'Nouveau compte'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $isEdit = $account !== null;
    $currentRole = old('role', $isEdit ? $account['role'] : $defaultRole);
    $roleLabels = config('smart_recruit.role_labels');
    $isOwnAccount = $isEdit && $account['id'] === $user['id'];
?>

<section class="form-shell wide">
    <div class="section-title">
        <h1><?php echo e($isEdit ? 'Modifier le compte' : 'Créer un compte'); ?></h1>
        <a href="<?php echo e(route('admin.users.index')); ?>">Retour à la liste</a>
    </div>
    <p class="muted"><?php echo e($isEdit ? 'Actualisez les informations du compte et vérifiez ses accès.' : 'Renseignez les informations de la personne et choisissez son rôle dans SmartRecruit.'); ?></p>

    <form class="form-card" method="post" action="<?php echo e($isEdit ? route('admin.users.update', $account['id']) : route('admin.users.store')); ?>">
        <?php echo csrf_field(); ?>
        <div class="form-section">
            <h2>Informations du compte</h2>
            <div class="grid-2">
                <label>
                    Nom complet
                    <input name="name" value="<?php echo e(old('name', $isEdit ? $account['name'] : '')); ?>" required>
                </label>
                <label>
                    Adresse e-mail
                    <input type="email" name="email" value="<?php echo e(old('email', $isEdit ? $account['email'] : '')); ?>" required>
                </label>
            </div>
        </div>

        <div class="form-section">
            <h2>Accès</h2>
            <label>
                Rôle
                <?php if($isOwnAccount): ?>
                    <input value="<?php echo e($roleLabels[$currentRole] ?? $currentRole); ?>" disabled>
                    <input type="hidden" name="role" value="<?php echo e($currentRole); ?>">
                <?php else: ?>
                    <select name="role" required>
                        <?php $__currentLoopData = $roleLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($value); ?>" <?php if($currentRole === $value): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                <?php endif; ?>
            </label>
            <p class="field-help">Le rôle détermine les espaces et les actions accessibles à ce compte.</p>
        </div>

        <div class="form-section">
            <h2>Informations du recruteur</h2>
            <p class="field-help">Complétez ces champs pour un compte recruteur.</p>
            <div class="grid-2">
                <label>
                    Raison sociale <span>(recruteur)</span>
                    <input name="company_name" value="<?php echo e(old('company_name', $isEdit ? $account['company_name'] : '')); ?>">
                </label>
                <label>
                    Poste occupé <span>(recruteur)</span>
                    <input name="position" value="<?php echo e(old('position', $isEdit ? $account['position'] : '')); ?>">
                </label>
                <label>
                    Site web <span>(recruteur)</span>
                    <input type="url" name="website" value="<?php echo e(old('website', $isEdit ? $account['website'] : '')); ?>">
                </label>
            </div>
        </div>

        <?php if($isEdit): ?>
            <?php if($account['student_id']): ?>
                <div class="form-section">
                    <h2>Profil candidat</h2>
                    <p class="field-help">
                        Le parcours et le CV se modifient depuis le profil lié à ce compte.
                        <a href="<?php echo e(route('students.edit', $account['student_id'])); ?>">Modifier le profil candidat</a>
                    </p>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="form-section">
                <h2>Profil candidat</h2>
                <label>
                    Titre du profil <span>(candidat)</span>
                    <input name="headline" value="<?php echo e(old('headline')); ?>" placeholder="Développeur Full-Stack, Data Scientist...">
                </label>
                <p class="field-help">Pour un compte candidat, indiquez le domaine de formation ou le métier recherché.</p>
            </div>
        <?php endif; ?>

        <?php if (! ($isEdit)): ?>
            <div class="form-section">
                <h2>Mot de passe</h2>
                <div class="grid-2">
                    <label>
                        Mot de passe
                        <input type="password" name="password" required minlength="8">
                    </label>
                    <label>
                        Confirmer le mot de passe
                        <input type="password" name="password_confirmation" required minlength="8">
                    </label>
                </div>
                <p class="field-help">Utilisez au moins 8 caractères et confirmez le mot de passe à l'identique.</p>
            </div>
        <?php endif; ?>

        <button class="button" type="submit"><?php echo e($isEdit ? 'Enregistrer les modifications' : 'Créer le compte'); ?></button>
    </form>

    <p class="field-help">
        Le champ « raison sociale » n'est requis que pour un recruteur, « titre du profil » que pour un candidat.
        Le profil correspondant est créé automatiquement selon le rôle choisi.
        <?php if($isOwnAccount): ?>
            Votre propre rôle n'est pas modifiable, afin d'éviter de vous retirer vos droits.
        <?php endif; ?>
    </p>

    <?php if($isEdit): ?>
        <div class="panel">
            <form class="form-card" method="post" action="<?php echo e(route('admin.users.password', $account['id'])); ?>">
                <?php echo csrf_field(); ?>
                <div class="form-section">
                    <h2>Réinitialiser le mot de passe</h2>
                    <p class="field-help">Renseignez un nouveau mot de passe d'au moins 8 caractères pour ce compte.</p>
                    <div class="grid-2">
                        <label>
                            Nouveau mot de passe
                            <input type="password" name="password" required minlength="8">
                        </label>
                        <label>
                            Confirmer le mot de passe
                            <input type="password" name="password_confirmation" required minlength="8">
                        </label>
                    </div>
                </div>
                <button class="button secondary" type="submit">Réinitialiser le mot de passe</button>
            </form>
        </div>
    <?php endif; ?>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH H:\smart-recruit\resources\views/admin/users/form.blade.php ENDPATH**/ ?>