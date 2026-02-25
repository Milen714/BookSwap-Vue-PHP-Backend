<?php

?>



<div class="book-modal flex flex-col mt-10 w-[90%] md:w-3/4 mx-auto p-6 rounded-md shadow-md bg-colors">
    <div id="bookOverview" class="StepTwo flex flex-col  w-full rounded-md shadow-md ">

        <d class="flex flex-col gap-4  p-6 border-b-2 border-[#2C3233]  mt-6">
            <div class="flex flex-col md:flex-row gap-4 justify-between">
                <div class="flex flex-col md:flex-row  gap-4">
                    <div id=" preview_image" class="min-w-[116px] max-w-[116px] min-h-[160px] max-h-[160px] bg-center"
                        style="background-image: url('<?php echo htmlspecialchars($book->cover_image_url); ?>')">
                    </div>
                    <div class="flex flex-col justify-start items-start">
                        <div class="flex flex-col gap-1 justify-start mb-2">
                            <span
                                class="rounded-full bg-[#e5e5e5] dark:bg-[#151819] border border-[#ccc] dark:border-[#2C3233] px-2 py-1 text-sm font-semibold text-colors">ISBN:
                                <?php echo htmlspecialchars($book->isbn); ?></span>
                            <h2 class="text-left text-lg font-semibold leading-none tracking-tight text-colors">
                                <?php echo htmlspecialchars($book->title); ?></h2>
                        </div>
                        <span id="preview_author"
                            class="text-md text-[#555] dark:text-[#7b8186] font-medium"><?php echo htmlspecialchars($book->author); ?></span>
                        <div class="flex flex-col gap-4 items-center">
                            <span class="text-md text-blue-600 font-medium">Shared by:
                                <?php echo htmlspecialchars($book->shared_by->fname). " " . htmlspecialchars($book->shared_by->lname) . " "; ?></span>
                            <span
                                class="rounded-full bg-[#e5e5e5] dark:bg-[#151819] border border-[#ccc] dark:border-[#2C3233] px-2 py-1 text-sm font-semibold text-colors">CONDITION:
                                <?php echo htmlspecialchars($book->condition->label()); ?></span>
                        </div>
                        <!-- <p id="preview_description" class="text-sm text-[#7b8186] mb-2">
                            <?php echo htmlspecialchars($book->description); ?>
                        </p> -->
                    </div>
                </div>
                <div>
                    <div class="flex flex-col items-start">
                        <h3 class="text-lg font-semibold mb-2 text-colors">Book Details</h3>
                        <ul class="text-left text-sm text-[#555] dark:text-[#7b8186] list-disc list-inside">
                            <li><strong>Genre:</strong> <?php echo htmlspecialchars($book->genre); ?></li>
                            <li><strong>Published Year:</strong> <?php echo htmlspecialchars($book->published_year); ?>
                            </li>
                            <li><strong>Page Count:</strong> <?php echo htmlspecialchars($book->page_count); ?></li>
                            <li><strong>Owner's Location:</strong>
                                <?php echo htmlspecialchars($book->shared_by->state); ?></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="book-modal-description ">
                <h3 class="text-lg font-semibold mb-2 text-colors">Book overview</h3>
                <p class="text-left text-sm text-[#555] dark:text-[#7b8186]">
                    <?php echo nl2br( $book->description == null ? "No description available." : htmlspecialchars($book->description)); ?>
                </p>
            </div>
            <?php if($book->owner_review !== null): ?>
            <div class="book-modal-description ">
                <h3 class="text-lg font-semibold mb-2 text-colors">Owner's Review</h3>
                <p class="text-left text-sm text-[#555] dark:text-[#7b8186]">
                    <?php echo nl2br( $book->owner_review == null ? "No review available." : htmlspecialchars($book->owner_review)); ?>
                </p>
            </div>
            <?php endif; ?>
            <?php if(str_contains($_SERVER["REQUEST_URI"], '/requesteeDetails')=== false): ?>
            <div>
                <div class="flex flex-col items-start">
                    <h3 class="text-lg font-semibold mb-2 text-colors">How it works</h3>
                    <div class="flex flex-row gap-4 items-center ml-4 mb-2">
                        <span class="text-blue-600 text-lg font-bold">1</span>
                        <div class="flex flex-col items-start">
                            <h3 class="text-lg font-semibold text-colors">Request the book</h3>
                            <p class="text-sm text-[#555] dark:text-[#7b8186]">The current owner will be notified of
                                your interest and
                                can
                                approve or decline your request.</p>
                        </div>
                    </div>
                    <div class="flex flex-row gap-4 items-center ml-4 mb-2">
                        <span class="text-blue-600 text-lg font-bold">2</span>
                        <div class="flex flex-col items-start">
                            <h3 class="text-lg font-semibold text-colors">Owner Accepts</h3>
                            <p class="text-left text-sm text-[#555] dark:text-[#7b8186]">As soon as it's approved, pay
                                the postage fee
                                and
                                track your
                                shipment. If both sides agree, a physical meet can be arranged, instead of delivery.</p>
                        </div>
                    </div>
                    <div class="flex flex-row gap-4 items-center ml-4 mb-2">
                        <span class="text-blue-600 text-lg font-bold">3</span>
                        <div class="flex flex-col items-start">
                            <h3 class="text-lg font-semibold text-colors">Track delivery</h3>
                            <p class="text-sm text-[#555] dark:text-[#7b8186]">Follow the shipment status and estimated
                                delivery date.
                                Mark
                                the book as received once it arrives.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-col md:flex-row gap-4 mt-4">
                <button class="button_neutral" type="button" onclick="closeBookModal()">Close</button>

                <?php if(isset($_SESSION['loggedInUser']) && $book->shared_by->id == $_SESSION['loggedInUser']->id): ?>

                <button disabled class="button_disabled" title="You need to be logged in to request a book."><span
                        class="text-red-500">You cannot request
                        your own book</span></button>

                <?php elseif(isset($_SESSION['loggedInUser']) && $_SESSION['loggedInUser']->swapTokens > 0): ?>

                <button class="button_primary" onclick="openAddressForm(<?php echo $book->id; ?>)">Request Book</button>



                <?php elseif(!isset($_SESSION['loggedInUser'])): ?>

                <a href="/login" class="button_disabled" title="You need to be logged in to request a book."><span
                        class="text-red-500">Login to request</span></a>
                <?php else: ?>

                <button class="button_disabled" disabled title="You need at least 1 swap token to request a book."><span
                        class="text-red-500">Insufficient swap tokens</span></button>
                <?php endif; ?>

                <?php else: ?>

                <button class="button_neutral" type="button" onclick="closeBookModal()">Close</button>
            </div>
            <?php endif; ?>
    </div>
</div>
<!-- Address Form -->

<section id="addressForm" class="flex flex-col mt-6 hidden">
    <h2 class="text-xl font-bold mb-4 text-colors">Shipping Address</h2>
    <div class="flex flex-col items-start">
        <label for="useProfileAddress" class="mb-2 text-sm font-medium text-colors">
            <input type="checkbox" id="useProfileAddress" name="useProfileAddress"
                onchange="toggleAddressForm(<?php echo isset($_SESSION['loggedInUser']) ? $_SESSION['loggedInUser']->id : 'null'; ?>)">
            Use my profile address
        </label>
    </div>
    <form class="flex flex-col gap-4">
        <input type="hidden" id="bookIdHidden" name="bookId" value="<?php echo $book->id ?>">
        <input type="hidden" id="ownerIdHidden" name="ownerId" value="<?php echo $book->shared_by->id ?>">
        <input type="hidden" id="requesterIdHidden" name="requesterId"
            value="<?php echo isset($_SESSION['loggedInUser']) ? $_SESSION['loggedInUser']->id : ''; ?>">

        <div class="flex flex-col items-start">
            <label for="street" class="mb-1 text-sm font-medium text-colors">Street Address</label>
            <input type="text" id="street" name="street"
                class="p-2 rounded-md bg-[#e5e5e5] dark:bg-[#2C3233] border border-[#ccc] dark:border-[#2C3233] text-colors focus:ring-blue-500 focus:border-blue-500"
                placeholder="123 Main St">
        </div>
        <div class="flex flex-col items-start">
            <label for="city" class="mb-1 text-sm font-medium text-colors">City</label>
            <input type="text" id="city" name="city"
                class="p-2 rounded-md bg-[#e5e5e5] dark:bg-[#2C3233] border border-[#ccc] dark:border-[#2C3233] text-colors focus:ring-blue-500 focus:border-blue-500"
                placeholder="City">
        </div>
        <div class="flex flex-col items-start">
            <label for="state" class="mb-1 text-sm font-medium text-colors">State/Province</label>
            <input type="text" id="state" name="state"
                class="p-2 rounded-md bg-[#e5e5e5] dark:bg-[#2C3233] border border-[#ccc] dark:border-[#2C3233] text-colors focus:ring-blue-500 focus:border-blue-500"
                placeholder="State/Province">
        </div>
        <div class="flex flex-col items-start">
            <label for="zip" class="mb-1 text-sm font-medium text-colors">ZIP/Postal Code</label>
            <input type="text" id="zip" name="zip"
                class="p-2 rounded-md bg-[#e5e5e5] dark:bg-[#2C3233] border border-[#ccc] dark:border-[#2C3233] text-colors focus:ring-blue-500 focus:border-blue-500"
                placeholder="ZIP/Postal Code">
        </div>
        <div class="flex flex-col items-start">
            <label for="country" class="mb-1 text-sm font-medium text-colors">Country</label>
            <input type="text" id="country" name="country"
                class="p-2 rounded-md bg-[#e5e5e5] dark:bg-[#2C3233] border border-[#ccc] dark:border-[#2C3233] text-colors focus:ring-blue-500 focus:border-blue-500"
                placeholder="Country">
        </div>
        <button class="button_primary mt-4" type="button" onclick="submitAddressForm()">Request Book</button>
    </form>
</section>
</div>