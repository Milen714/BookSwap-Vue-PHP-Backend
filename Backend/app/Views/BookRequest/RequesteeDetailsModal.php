<?php 

namespace App\Views\BookRequest;
?>
<div class="text-white bg-black p-4 rounded">HELLO FROM THE DETAILS</div>

<!-- Book Request Modal -->
<div class="book-modal flex flex-col mt-10 w-3/4 mx-auto p-6 rounded-md shadow-md bg-[#0F0F0F]">
    <div id="bookOverview" class="StepTwo flex flex-col  w-full rounded-md shadow-md ">

        <div class="flex flex-col gap-4  p-6 border-b-2 border-[#2C3233]  mt-6">
            <div class="flex flex-row gap-4 justify-between">
                <div class="flex flex-row gap-4">
                    <div id=" preview_image" class="min-w-[116px] max-w-[116px] min-h-[160px] max-h-[160px] bg-center"
                        style="background-image: url('<?php echo htmlspecialchars($request->book->cover_image_url); ?>')">
                    </div>
                    <div class="flex flex-col justify-start items-start">
                        <div class="flex flex-col gap-1 justify-start mb-2">
                            <span
                                class="rounded-full bg-[#151819] border border-[#2C3233] px-2 py-1 text-sm font-semibold text-gray-200">ISBN:
                                <?php echo htmlspecialchars($request->book->isbn); ?></span>
                            <h2 class="text-left text-lg font-semibold leading-none tracking-tight ">
                                <?php echo htmlspecialchars($request->book->title); ?></h2>
                        </div>
                        <span id="preview_author"
                            class="text-md text-[#7b8186] font-medium"><?php echo htmlspecialchars($request->book->author); ?></span>
                        <div class="flex flex-col gap-4 items-center">
                            <span class="text-md text-blue-600 font-medium">Shared by:
                                <?php echo htmlspecialchars($request->book->shared_by->fname). " " . htmlspecialchars($request->book->shared_by->lname) . " "; ?></span>
                            <span
                                class="rounded-full bg-[#151819] border border-[#2C3233] px-2 py-1 text-sm font-semibold text-gray-200">CONDITION:
                                <?php echo htmlspecialchars($request->book->condition->label()); ?></span>
                        </div>
                        <!-- <p id="preview_description" class="text-sm text-[#7b8186] mb-2">
                            <?php echo htmlspecialchars($request->book->description); ?>
                        </p> -->
                    </div>
                </div>
                <div>
                    <div class="flex flex-col items-start">
                        <h3 class="text-lg font-semibold mb-2">Book Details</h3>
                        <ul class="text-left text-sm text-[#7b8186] list-disc list-inside">
                            <li><strong>Genre:</strong> <?php echo htmlspecialchars($request->book->genre); ?></li>
                            <li><strong>Published Year:</strong>
                                <?php echo htmlspecialchars($request->book->published_year); ?>
                            </li>
                            <li><strong>Page Count:</strong> <?php echo htmlspecialchars($request->book->page_count); ?>
                            </li>
                            <li><strong>Owner's Location:</strong>
                                <?php echo htmlspecialchars($request->book->shared_by->state); ?></li>
                        </ul>
                    </div>
                </div>
            </div>


            <div>
                <div class="flex flex-col items-start">
                    <h3 class="text-lg font-semibold mb-2">How it works</h3>
                    <div class="flex flex-row gap-4 items-center ml-4 mb-2">
                        <span class="text-blue-600 text-lg font-bold">1</span>
                        <div class="flex flex-col items-start">
                            <h3 class="text-lg font-semibold">Request the book</h3>
                            <p class="text-sm text-[#7b8186]">The current owner will be notified of your interest and
                                can
                                approve or decline your request.</p>
                        </div>
                    </div>
                    <div class="flex flex-row gap-4 items-center ml-4 mb-2">
                        <span class="text-blue-600 text-lg font-bold">2</span>
                        <div class="flex flex-col items-start">
                            <h3 class="text-lg font-semibold">Owner Accepts</h3>
                            <p class="text-left text-sm text-[#7b8186]">As soon as it's approved, pay the postage fee
                                and
                                track your
                                shipment. If both sides agree, a physical meet can be arranged, instead of delivery.</p>
                        </div>
                    </div>
                    <div class="flex flex-row gap-4 items-center ml-4 mb-2">
                        <span class="text-blue-600 text-lg font-bold">3</span>
                        <div class="flex flex-col items-start">
                            <h3 class="text-lg font-semibold">Track delivery</h3>
                            <p class="text-sm text-[#7b8186]">Follow the shipment status and estimated delivery date.
                                Mark
                                the book as received once it arrives.</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>