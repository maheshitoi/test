    <div class="background-image" style="background-image: url(<?php echo BASEURL ?>assets/img/page_profile_img.jpg);margin-top: -50px;">
        <div class="profile-title text-center">Become a Member
        </div>
    </div>
    </div>

    <section class="background-image space-extra" style="background-image: url(assets/img/22630829_6552436.jpg);" id="show_msg">
        <h2 class="_title_decorator_target" style="text-align:center;">Registration</h2>
        <div class="container">
            <?php $validation = \Config\Services::validation(); ?>
            <form action="<?= BASEURL; ?>/member_req" method="post" id="memberForm">
                <div class="row member_box">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" id="name" name="name" class="form-control" placeholder="Enter Your Name">
                        </div>
                        <div class="form-group">
                            <label for="dob">Date of Birth</label>
                            <input type="date" id="dob" name="dob" class="form-control"><i class="fa fa-calendar-alt"></i>
                        </div>

                        <div class="form-group">
                            <label for="aadhar_no">Aadhar No.</label>
                            <input type="number" id="aadhar_no" name="aadhar_no" class="form-control" placeholder="Enter Your Aadhar No">
                        </div>
                        <div class="form-group">
                            <label for="present_address">Present Address</label>
                            <input type="text" id="present_address" name="present_address" class="form-control" placeholder="Enter Your Present Address">
                        </div>
                        <div class="form-group">
                            <label for="office_designation">Office Designation</label>
                            <input type="text" id="office_designation" name="office_designation" class="form-control" placeholder="Enter Your Office Designation">
                        </div>
                        <div class="form-group">
                            <label for="service_details">Service Details</label>
                            <select id="service_details" name="service_details" class="form-control">
                                <option value="dentist">I am Working Now</option>
                                <option value="student">I am Retired Person</option>
                                <option value="other">I am Student Now</option>
                                <option value="other">I am an Entrepreneur</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="profile_image">Upload Profile Image</label>
                            <input type="file" id="profile_image" name="profile_image" class="form-control" accept="image/*">
                            <div id="profile_image_preview" style="margin-top: 10px;"></div>
                            <div id="profile_image_error" class="text-danger" style="display: none; margin-top: 5px;">Please Upload Your Profile Image</div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="father_name">Father Name</label>
                            <input type="text" id="father_name" name="father_name" class="form-control" placeholder="Enter Your Father Name">
                        </div>
                        <div class="form-group">
                            <label for="email_id">Email</label>
                            <input type="email" id="email_id" name="email_id" class="form-control" placeholder="Enter Your Email Id">
                            <?php if (isset($errors['email_id'])): ?>
                                <div class="text-danger" style="font-size: 14px;"><?= $errors['email_id']; ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="mobile_no_input">Mobile No</label>
                            <div class="d-flex">
                                <select id="country_code" name="country_code" class="form-control" style="max-width: 120px; margin-right: 10px;">
                                    <option value="+91" selected>+91 (IN)</option>
                                    <option value="+1">+1 (US)</option>
                                    <option value="+44">+44 (UK)</option>
                                    <option value="+61">+61 (AU)</option>
                                    <option value="+971">+971 (UAE)</option>
                                </select>
                                <input type="tel" id="mobile_no_input" name="mobile_no_input" class="form-control" placeholder="Enter Your Mobile No">
                            </div>
                            <input type="hidden" id="mobile_no" name="mobile_no">
                            <?php if (isset($errors['mobile_no'])): ?>
                                <div class="text-danger" style="font-size: 14px;"><?= $errors['mobile_no']; ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="permanent_address">Permanent Address</label>
                            <input type="text" id="permanent_address" name="permanent_address" class="form-control" placeholder="Enter Your Permanent Address">
                        </div>
                        <div class="form-group">
                            <label for="certification_no">Certification No</label>
                            <input type="text" id="certification_no" name="certification_no" class="form-control" placeholder="Enter Your Certification No">
                        </div>
                        <div class="form-group">
                            <label for="certification_document">Certification Image</label>
                            <input type="file" id="certification_doc" name="certification_document" class="form-control" accept="image/*">
                            <div id="certification_doc_preview" style="margin-top: 10px;"></div>
                            <div id="certification_doc_error" class="text-danger" style="display: none; margin-top: 5px;">Please Upload Your Certification</div>
                        </div>
                    </div>
                    <div class="col-12 d-flex justify-content-center my-4">
                        <div style="background: linear-gradient(135deg,rgb(167, 219, 215),rgb(165, 205, 203)); color: #000000; padding: 20px 30px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); max-width: 400px; text-align: center;">
                            <div style="font-size: 2rem; font-weight: bold; display: flex; align-items: center; justify-content: center;">
                                <i class="fa fa-wallet" style="margin-right: 10px;"></i> ₹200
                            </div>
                            <div style="margin-top: 10px; font-size: 1rem;">
                                <strong>Registration Amount</strong>
                            </div>
                        </div>
                    </div>

                    <div class="form-group" style="text-align:center;margin:30px 0px">
                        <button type="submit" onclick="submitForm(event)" class="th-btn">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <style>
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Firefox */
        input[type=number] {
            -moz-appearance: textfield;
        }

        .hide-me {
            display: none;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 14px;
            /* Adjust font size as needed */
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>

    <script>
        function submitForm(event) {
            event.preventDefault();

            // ✅ Combine country code and mobile number
            const countryCode = document.getElementById('country_code').value;
            const mobileInput = document.getElementById('mobile_no_input').value.trim();
            document.getElementById('mobile_no').value = countryCode + mobileInput;

            let isValid = true;
            let firstInvalid = null;

            const form = document.getElementById("memberForm");
            const inputs = form.querySelectorAll("input, select, textarea");

            // Corrected Profile Image Error ID
            const imageInput = document.getElementById("profile_image");
            const imageError = document.getElementById("profile_image_error");

            inputs.forEach(input => {
                if ((input.type !== "file" && input.value.trim() === "") ||
                    (input.type === "file" && input.id === "profile_image" && input.files.length === 0)) {

                    isValid = false;
                    if (!firstInvalid) firstInvalid = input;

                    input.style.borderColor = "#dd3636";
                    input.classList.add("placeholder-red");

                    if (!input.placeholder && input.type !== "file") {
                        input.placeholder = "This field is required";
                    }

                    // Show image error only for #profile_image
                    if (input.id === "profile_image" && imageError) {
                        imageError.style.display = "block";
                    }

                } else {
                    input.style.borderColor = "#3AAFA9";
                    input.style.backgroundColor = "#f3f7fb";

                    input.classList.remove("placeholder-red");

                    // Hide image error if an image is selected
                    if (input.id === "profile_image" && imageError) {
                        imageError.style.display = "none";
                    }
                }
            });

            if (!isValid && firstInvalid) {
                firstInvalid.focus();
            } else {
                form.submit(); // Submit when valid
            }
        }

        // Green border on typing/selecting
        window.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById("memberForm");
            const inputs = form.querySelectorAll("input, select, textarea");

            inputs.forEach(input => {
                input.addEventListener('input', () => {
                    if (input.type !== "file" && input.value.trim() !== "") {
                        input.style.borderColor = "#3AAFA9";
                        input.classList.remove("placeholder-red");
                    }
                });

                input.addEventListener('change', () => {
                    if ((input.type === "file" && input.files.length > 0) ||
                        (input.type !== "file" && input.value.trim() !== "")) {
                        input.style.borderColor = "#3AAFA9";
                        input.classList.remove("placeholder-red");
                    }
                });
            });
        });

        document.addEventListener("DOMContentLoaded", function() {
            const duplicateModal = new bootstrap.Modal(document.getElementById('duplicateEntryModal'));
            <?php if (isset($errors['email_id']) || isset($errors['mobile_no'])): ?>
                duplicateModal.show();
            <?php endif; ?>
        });

        document.addEventListener('DOMContentLoaded', function() {

            // Date of Birth Picker Fallback
            const dobInput = document.getElementById('dob');
            if (dobInput) {
                dobInput.addEventListener('focus', function() {
                    if (this.showPicker) {
                        this.showPicker();
                    } else {
                        this.type = 'date';
                    }
                });
            }
        });

        const baseURL = "<?php echo BASEURL; ?>";

        document.addEventListener("DOMContentLoaded", function() {
            // Get all file input fields that accept images
            document.querySelectorAll('input[type="file"][accept="image/*"]').forEach(input => {
                input.addEventListener("change", async (event) => {
                    const file = event.target.files[0];
                    if (!file) return;

                    const formData = new FormData();
                    formData.append('files', file);

                    try {
                        const response = await fetch(`${baseURL}/api/v1/uploadFile`, {
                            method: 'POST',
                            body: formData,
                        });

                        const result = await response.json();
                        console.log("Upload Response:", result); // Debugging

                        if (response.ok && result.statusCode === 200 && result.result) {
                            const imageUrl = result.result.file_path;

                            // Set hidden input value for file name (if needed)
                            let hiddenInput = document.getElementById(input.id + "_hidden");
                            if (!hiddenInput) {
                                hiddenInput = document.createElement("input");
                                hiddenInput.type = "hidden";
                                hiddenInput.id = input.id + "_hidden";
                                hiddenInput.name = input.name + "_hidden";
                                input.parentElement.appendChild(hiddenInput);
                            }
                            hiddenInput.value = result.result.file_name;

                            // Display Image Preview
                            let previewContainer = document.getElementById(input.id + "_preview");
                            if (previewContainer) {
                                previewContainer.innerHTML = ''; // Clear previous preview
                                let imgElement = document.createElement('img');
                                imgElement.src = imageUrl;
                                imgElement.alt = "Uploaded Image";
                                imgElement.className = "img-thumbnail";
                                imgElement.style.maxWidth = "150px";
                                previewContainer.appendChild(imgElement);
                            }
                        } else {
                            console.error("Upload Error:", result);
                        }
                    } catch (error) {
                        console.error('Fetch Error:', error);
                    }
                });
            });
        });

        // Save input to localStorage on input/change
        document.addEventListener("DOMContentLoaded", function() {
            const form = document.getElementById("memberForm");
            const inputs = form.querySelectorAll("input, select, textarea");

            inputs.forEach(input => {
                const key = "form_" + input.name;

                // Restore saved value on load
                const savedValue = localStorage.getItem(key);
                if (savedValue) {
                    if (input.type === "file") return; // skip file inputs
                    input.value = savedValue;
                    if (input.id === "mobile_no_input") {
                        const countryCode = document.getElementById("country_code").value;
                        document.getElementById("mobile_no").value = countryCode + savedValue;
                    }
                }

                // Save to localStorage on change
                input.addEventListener("input", () => {
                    if (input.type !== "file") {
                        localStorage.setItem(key, input.value);
                    }
                });

                input.addEventListener("change", () => {
                    if (input.type !== "file") {
                        localStorage.setItem(key, input.value);
                    }
                });
            });

            // Save country code change (for mobile number)
            const countryCode = document.getElementById("country_code");
            if (countryCode) {
                const key = "form_country_code";
                const saved = localStorage.getItem(key);
                if (saved) countryCode.value = saved;

                countryCode.addEventListener("change", () => {
                    localStorage.setItem(key, countryCode.value);
                });
            }
        });
    </script>



    <style>
        /* Red placeholder text */
        .placeholder-red::placeholder {
            color: #dd3636 !important;
        }
    </style>



    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Bootstrap 5 CSS & JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>