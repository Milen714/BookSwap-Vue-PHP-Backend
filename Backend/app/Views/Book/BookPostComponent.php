<?php

?>


<article class="flip-card shadow-md shadow-black/20 dark:shadow-white/20">

    <div class="card-flip-container">
        <div class="card-flip">
            <div class="card-flip-front  bookcard_body bg-cover bg-center"
                style="background-image: url('<?php echo $book->thumbnail_image_url ? htmlspecialchars($book->thumbnail_image_url) : '/Assets/NoImagePlaceholder.png'; ?>')">
                <div class="  h-full flex flex-col justify-end">
                    <div class="bg-[#ffffffcc] dark:bg-[#000000cc] w-full p-4 text-center rounded-bl-md rounded-br-md">
                        <h4 class="text-md font-semibold text-colors"><?php echo htmlspecialchars($book->title); ?></h4>
                        <div class="flex flex-row justify-end mt-2 gap-1">
                            <button
                                class="rounded-xl bg-[#e5e5e5] dark:bg-[#151819] hover:bg-[#d0d0d0] dark:hover:bg-[#2C3233] border border-[#ccc] dark:border-[#2C3233] px-2 py-1 text-sm font-semibold text-colors">Quick
                                View
                            </button>
                            <?php if($_SERVER["REQUEST_URI"] === '/' || str_contains($_SERVER["REQUEST_URI"], '?genre=')): ?>
                            <button onclick="openBookDetails(<?php echo htmlspecialchars($book->id); ?>, false)"
                                class="rounded-xl bg-[#e5e5e5] dark:bg-[#151819] hover:bg-[#d0d0d0] dark:hover:bg-[#2C3233] border border-[#ccc] dark:border-[#2C3233] px-2 py-1 text-sm font-semibold text-colors">Get
                                this book</button>
                            <?php endif; ?>
                            <?php if(str_contains($_SERVER["REQUEST_URI"], '/myRequests/')): ?>
                            <button
                                onclick="openRequesteeDetails(<?php echo htmlspecialchars($_SESSION['loggedInUser']->id); ?>, <?php echo htmlspecialchars($request->id); ?>)"
                                class="rounded-xl bg-[#e5e5e5] dark:bg-[#151819] hover:bg-[#d0d0d0] dark:hover:bg-[#2C3233] border border-[#ccc] dark:border-[#2C3233] px-2 py-1 text-sm font-semibold text-colors">Request
                                Actions</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-flip-back bookcard_body bg-cover bg-center hidden"
                style="background-image: url('<?php echo $book->thumbnail_image_url ? htmlspecialchars($book->thumbnail_image_url) : '/Assets/NoImagePlaceholder.png'; ?>')">
                <div class="bg-[#f2f0efe6] dark:bg-[#191b1de6] h-full flex flex-col justify-start rounded-md">
                    <div class=" w-full p-4 text-center rounded-bl-md rounded-br-md overflow-hidden h-full">
                        <h4 class="text-md font-semibold text-colors"><?php echo htmlspecialchars($book->title); ?></h4>
                        <div class="flex flex-row justify-between gap-1 mt-3">
                            <span
                                class="text-sm font-medium text-colors"><?php echo htmlspecialchars($book->author); ?></span>
                            <span
                                class="text-sm font-medium text-colors"><?php echo htmlspecialchars($book->published_year); ?></span>
                            <span
                                class="text-sm font-medium text-colors"><?php echo htmlspecialchars($book->page_count); ?></span>
                        </div>
                        <div class="flex flex-row justify-center mt-3">
                            <span
                                class="rounded-full bg-[#e5e5e5] dark:bg-[#151819] border border-[#ccc] dark:border-[#2C3233] px-2 py-1 text-sm font-semibold text-colors"><?php echo htmlspecialchars($book->genre); ?></span>
                        </div>
                        <div class="flex flex-col justify-between ">
                            <div class="bookcard_description">
                                <p class="text-sm font-regular text-colors">
                                    <?php echo htmlspecialchars($book->description); ?></p>
                            </div>
                            <div class="flex flex-row justify-between items-center mt-4">
                                <span
                                    class="text-sm font-medium text-colors"><?php echo htmlspecialchars($book->condition->label()); ?></span>
                                <span
                                    class="text-sm font-medium text-colors"><?php echo htmlspecialchars($book->shared_by->fname . ' ' . $book->shared_by->lname); ?></span>
                                <button
                                    class="rounded-xl bg-[#e5e5e5] dark:bg-[#151819] border border-[#ccc] dark:border-[#2C3233] px-2 py-1 text-sm font-semibold text-colors">Flip</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</article>