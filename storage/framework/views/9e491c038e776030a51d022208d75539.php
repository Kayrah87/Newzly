<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php $__env->slot('header', null, []); ?> 
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <?php echo e($newsletter->name); ?>

            </h2>
            <div class="space-x-2">
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $newsletter)): ?>
                    <a href="<?php echo e(route('newsletters.edit', $newsletter)); ?>" class="text-gray-600 hover:text-gray-900">Edit</a>
                <?php endif; ?>
                <a href="<?php echo e(route('newsletters.issues.index', $newsletter)); ?>" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md">
                    View Issues
                </a>
            </div>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <?php if(session('success')): ?>
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    <?php echo e(session('success')); ?>

                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Newsletter Info -->
                <div class="md:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">Newsletter Information</h3>
                            <div class="space-y-3">
                                <div>
                                    <span class="text-gray-600 font-medium">Description:</span>
                                    <p class="text-gray-800"><?php echo e($newsletter->description ?? 'No description provided'); ?></p>
                                </div>
                                <div>
                                    <span class="text-gray-600 font-medium">Owner:</span>
                                    <p class="text-gray-800"><?php echo e($newsletter->owner->name); ?></p>
                                </div>
                                <div>
                                    <span class="text-gray-600 font-medium">Created:</span>
                                    <p class="text-gray-800"><?php echo e($newsletter->created_at->format('M d, Y')); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Issues -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold">Recent Issues</h3>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $newsletter)): ?>
                                    <a href="<?php echo e(route('newsletters.issues.create', $newsletter)); ?>" class="text-indigo-600 hover:text-indigo-800 text-sm">Create Issue</a>
                                <?php endif; ?>
                            </div>
                            <?php if($newsletter->issues->count() > 0): ?>
                                <div class="space-y-3">
                                    <?php $__currentLoopData = $newsletter->issues->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $issue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="border-l-4 border-indigo-500 pl-4 py-2">
                                            <a href="<?php echo e(route('newsletters.issues.show', [$newsletter, $issue])); ?>" class="font-medium text-gray-900 hover:text-indigo-600">
                                                <?php echo e($issue->title); ?>

                                            </a>
                                            <p class="text-sm text-gray-500"><?php echo e($issue->status); ?> • <?php echo e($issue->created_at->format('M d, Y')); ?></p>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            <?php else: ?>
                                <p class="text-gray-500">No issues created yet.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">Quick Stats</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Total Issues:</span>
                                    <span class="font-semibold"><?php echo e($newsletter->issues->count()); ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Editors:</span>
                                    <span class="font-semibold"><?php echo e($newsletter->editors()->count()); ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Recipients:</span>
                                    <span class="font-semibold"><?php echo e($newsletter->recipients()->count()); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manageEditors', $newsletter)): ?>
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold mb-4">Manage Team</h3>
                                <div class="space-y-2">
                                    <a href="<?php echo e(route('newsletters.editors', $newsletter)); ?>" class="block text-indigo-600 hover:text-indigo-800">
                                        Manage Editors
                                    </a>
                                    <a href="<?php echo e(route('newsletters.recipients', $newsletter)); ?>" class="block text-indigo-600 hover:text-indigo-800">
                                        Manage Recipients
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete', $newsletter)): ?>
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold mb-4 text-red-600">Danger Zone</h3>
                                <form method="POST" action="<?php echo e(route('newsletters.destroy', $newsletter)); ?>" onsubmit="return confirm('Are you sure you want to delete this newsletter? This action cannot be undone.');">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <?php if (isset($component)) { $__componentOriginal656e8c5ea4d9a4fa173298297bfe3f11 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal656e8c5ea4d9a4fa173298297bfe3f11 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.danger-button','data' => ['type' => 'submit']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('danger-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit']); ?>
                                        Delete Newsletter
                                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal656e8c5ea4d9a4fa173298297bfe3f11)): ?>
<?php $attributes = $__attributesOriginal656e8c5ea4d9a4fa173298297bfe3f11; ?>
<?php unset($__attributesOriginal656e8c5ea4d9a4fa173298297bfe3f11); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal656e8c5ea4d9a4fa173298297bfe3f11)): ?>
<?php $component = $__componentOriginal656e8c5ea4d9a4fa173298297bfe3f11; ?>
<?php unset($__componentOriginal656e8c5ea4d9a4fa173298297bfe3f11); ?>
<?php endif; ?>
                                </form>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php /**PATH /home/runner/work/Newzly/Newzly/resources/views/newsletters/show.blade.php ENDPATH**/ ?>