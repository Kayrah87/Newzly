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
                <?php echo e($issue->title); ?>

            </h2>
            <div class="space-x-2">
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $newsletter)): ?>
                    <a href="<?php echo e(route('newsletters.issues.edit', [$newsletter, $issue])); ?>" class="text-gray-600 hover:text-gray-900">Edit</a>
                    <a href="<?php echo e(route('newsletters.issues.articles.create', [$newsletter, $issue])); ?>" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md">
                        Add Article
                    </a>
                <?php endif; ?>
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
                <!-- Main Content -->
                <div class="md:col-span-2">
                    <!-- Issue Content -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">Issue Content</h3>
                            <?php if($issue->content): ?>
                                <div class="prose max-w-none">
                                    <?php echo $issue->content; ?>

                                </div>
                            <?php else: ?>
                                <p class="text-gray-500">No content added yet.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Articles -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">Articles</h3>
                            <?php if($issue->articles->count() > 0): ?>
                                <div class="space-y-6">
                                    <?php $__currentLoopData = $issue->articles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $article): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="border-l-4 border-indigo-500 pl-4">
                                            <div class="flex justify-between items-start mb-2">
                                                <h4 class="font-semibold text-lg"><?php echo e($article->title); ?></h4>
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $newsletter)): ?>
                                                    <div class="flex space-x-2">
                                                        <a href="<?php echo e(route('newsletters.issues.articles.edit', [$newsletter, $issue, $article])); ?>" class="text-gray-600 hover:text-gray-800 text-sm">Edit</a>
                                                        <form method="POST" action="<?php echo e(route('newsletters.issues.articles.destroy', [$newsletter, $issue, $article])); ?>" class="inline" onsubmit="return confirm('Are you sure?');">
                                                            <?php echo csrf_field(); ?>
                                                            <?php echo method_field('DELETE'); ?>
                                                            <button type="submit" class="text-red-600 hover:text-red-800 text-sm">Delete</button>
                                                        </form>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <p class="text-sm text-gray-600 mb-2">By <?php echo e($article->author->name ?? 'Unknown'); ?> • Order: <?php echo e($article->order); ?></p>
                                            <div class="prose max-w-none">
                                                <?php echo Str::limit($article->content, 300); ?>

                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            <?php else: ?>
                                <p class="text-gray-500">No articles added yet. Add your first article to get started!</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">Issue Details</h3>
                            <div class="space-y-3">
                                <div>
                                    <span class="text-gray-600 font-medium">Status:</span>
                                    <p class="capitalize px-2 py-1 rounded text-xs inline-block
                                        <?php if($issue->status === 'draft'): ?> bg-gray-200 text-gray-700
                                        <?php elseif($issue->status === 'scheduled'): ?> bg-blue-200 text-blue-700
                                        <?php else: ?> bg-green-200 text-green-700
                                        <?php endif; ?>">
                                        <?php echo e($issue->status); ?>

                                    </p>
                                </div>
                                <div>
                                    <span class="text-gray-600 font-medium">Created:</span>
                                    <p class="text-gray-800"><?php echo e($issue->created_at->format('M d, Y')); ?></p>
                                </div>
                                <?php if($issue->published_at): ?>
                                    <div>
                                        <span class="text-gray-600 font-medium">Published:</span>
                                        <p class="text-gray-800"><?php echo e($issue->published_at->format('M d, Y H:i')); ?></p>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <span class="text-gray-600 font-medium">Articles:</span>
                                    <p class="text-gray-800"><?php echo e($issue->articles->count()); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>
                            <div class="space-y-2">
                                <a href="<?php echo e(route('newsletters.issues.index', $newsletter)); ?>" class="block text-indigo-600 hover:text-indigo-800">
                                    Back to Issues
                                </a>
                                <a href="<?php echo e(route('newsletters.show', $newsletter)); ?>" class="block text-indigo-600 hover:text-indigo-800">
                                    View Newsletter
                                </a>
                            </div>
                        </div>
                    </div>

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $newsletter)): ?>
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold mb-4 text-red-600">Danger Zone</h3>
                                <form method="POST" action="<?php echo e(route('newsletters.issues.destroy', [$newsletter, $issue])); ?>" onsubmit="return confirm('Are you sure you want to delete this issue? This action cannot be undone.');">
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
                                        Delete Issue
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
<?php /**PATH /home/runner/work/Newzly/Newzly/resources/views/newsletters/issues/show.blade.php ENDPATH**/ ?>