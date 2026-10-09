<footer class="mt-auto bg-[#111827] py-10 text-gray-300">
    <div class="mx-auto grid max-w-7xl gap-8 px-4 text-sm sm:px-6 lg:grid-cols-3 lg:items-start lg:px-8">
        <div>
            <h2 class="font-bold text-white">Address</h2>
            <p class="mt-2 leading-relaxed text-gray-400"><?php echo e(config('store.address')); ?></p>
        </div>
        <div>
            <h2 class="font-bold text-white">Email</h2>
            <a href="mailto:admin@davaobosscomputer.com" class="mt-2 inline-block text-gray-400 transition hover:text-white">admin@davaobosscomputer.com</a>
        </div>
        <div>
            <h2 class="font-bold text-white">Contact</h2>
            <a href="tel:<?php echo e(config('store.phone')); ?>" class="mt-2 inline-block text-gray-400 transition hover:text-white"><?php echo e(config('store.phone')); ?></a>
        </div>
        <p class="border-t border-white/10 pt-5 text-xs text-gray-500 lg:col-span-3">© <?php echo e(date('Y')); ?> <?php echo e(config('store.name')); ?>. All Rights Reserved.</p>
    </div>
</footer>
<?php /**PATH C:\Users\Cyrus\Downloads\IT12\resources\views/layouts/footer.blade.php ENDPATH**/ ?>