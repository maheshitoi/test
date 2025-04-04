
<section class="background-image space-extra" style="background-image: url(assets/img/22630829_6552436.jpg);" id="show_msg">
    <h2 class="_title_decorator_target" style="text-align:center;">Registration</h2>
    <div class="container">
        <?php $validation = \Config\Services::validation(); ?>
        <form action="<?= BASEURL; ?>/member_req" method="post" id="memberForm">
            <div class="row member_box">
                <div class="col-lg-6">
                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <div class="d-flex">
                            <input type="text" id="title" class="form-control" style="margin-right: 5px;width: 12%;background-color: #f4fefe;padding: 0;text-align: center;" value="Dr." readonly>
                            <input type="text" id="first_name_input" class="form-control" placeholder="Enter Your First Name" oninput="combineFirstName()">
                        </div>
                        <input type="hidden" id="first_name" name="first_name">
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input type="text" id="last_name" name="last_name" class="form-control" placeholder="Enter Your Last Name">
                    </div>
                    <div class="form-group">
                        <label for="dob">Date of Birth</label>
                        <input type="date" id="dob" name="dob" class="form-control"><i class="fa fa-calendar-alt"></i>
                    </div>
                    <div class="form-group">
                        <label for="email_id">Email</label>
                        <input type="email" id="email_id" name="email_id" class="form-control" placeholder="Enter Your Email Id">
                        <div id="email_error" class="text-danger" style="display: none;padding: 10px 0;">Please enter a valid email address.
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="mobile_no">Mobile Number</label>
                        <div class="d-flex">
                            <input type="text" id="search_country_code" class="form-control" placeholder="Search for a country" style="margin-right: 5px;width: 50%;">
                            <input type="text" id="mobile_no_input" name="mobile_no_input" class="form-control" placeholder="Enter Your Mobile Number" oninput="updateMobileNo()">
                        </div>
                        <div id="country_list" class="country-list" style="display:none;"></div>
                        <input type="hidden" id="country_code" name="country_code">
                        <input type="hidden" id="mobile_no" name="mobile_no">
                        <small id="mobile-error" class="text-danger" style="display:none;">Please enter a valid mobile number.</small>
                    </div>
                    <div class="form-group">
                        <label for="college_name">College Name</label>
                        <input type="text" id="college_name" name="college_name" class="form-control" placeholder="Enter Your College">
                    </div>
                    <div class="form-group">
                        <label for="field">Field of Work</label>
                        <select id="field" name="field" class="form-control">
                            <option value="dentist">Dentist</option>
                            <option value="student">Student</option>
                            <option value="other">Others</option>
                        </select>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        <label for="reg_no">DCI Reg no</label>
                        <input type="text" id="reg_no" name="reg_no" class="form-control" placeholder="Enter Your Reg no">
                    </div>
                    <div class="form-group">
                        <label for="address">Address</label>
                        <input type="text" id="address" name="address" class="form-control" placeholder="Enter Your Address">
                    </div>
                    <div class="form-group">
                        <label for="city">City</label>
                        <input type="text" id="city" name="city" class="form-control" placeholder="Enter Your City">
                    </div>
                    <div class="form-group">
                        <label for="state">State</label>
                        <input type="text" id="state" name="state" class="form-control" placeholder="Enter Your State">
                    </div>
                    <div class="form-group">
                        <label for="country">Country</label>
                        <input type="text" id="country" name="country" class="form-control" placeholder="Enter Your Country" value="India">
                    </div>
                    <div class="form-group">
                        <label for="pincode">Postal Code</label>
                        <input type="number" id="pincode" name="pincode" class="form-control" placeholder="Enter Your Pincode" oninput="checkPinLength(this)">
                    </div>
                    <div class="form-group">
                        <label for="payment_mode">Payment Mode</label>
                        <select class="form-select" id="payment_mode" name="payment_mode">
                            <option value="1" selected>Online</option>
                            <!-- <option value="2">Cheque/DD</option> -->
                            <!-- <option value="3">Qr code</option> -->
                        </select>
                    </div>
                </div>
                <div class="col-sm-8 col-md-6 col-lg-5 col-xl-4 payment_detail aligncenter">
                    <table cellpadding="10" cellspacing="0">
                        <tr>
                            <td style="color: #4d5765;">Registration Amount</td>
                            <td>:</td>
                            <td style="color: #4d5765;">&#x20B9; 5000</td>
                        </tr>
                        <tr>
                            <td style="color: #4d5765;">GST 18%</td>
                            <td>:</td>
                            <td style="color: #4d5765;">&#x20B9; 900</td>
                        </tr>
                        <tr>
                            <td>Total Registration Amount</td>
                            <td>:</td>
                            <td>&#x20B9; 5900</td>
                        </tr>
                    </table>
                </div>
                <!-- Error message placeholder -->
                <div id="error-message" class="text-danger" style="display:none; text-align:center; margin-bottom:20px;"></div>
                <div style="text-align:center;">
                    <button type="submit" class="btn btn-primary">Proceed to Pay</button>
                </div>
                <div id="payment-options" style="display:none;">
                    <div id="Qr-payment" style="display:none;">
                        <div class="col-md-10 aligncenter" style="padding: 20px;">
                            <div class="col-lg-6 aligncenter py-5">
                                <img src="<?php echo BASEURL ?>assets\img\payment\qr_code.png">
                                <a href="upi://pay?pa=eazypay.0000034575@icici&amp;pn=M/S.INDIAN ASSOCIATION OF ORAL IMPLANTOLOGISTS &amp;tr=EZYS9840915849&amp;cu=INR&amp;mc=5047&amp;am=5900" class="blog-single float-start">Link to Pay</a>
                            </div>
                            <table>
                                <div class="form-group">
                                    <label for="screenshotfile">Choose a Payment Attachment</label>
                                    <input type="file" id="screenshotfile" name="screenshotfile" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="remarks">Message</label>
                                    <textarea name="remarks" id="remarks" cols="30" rows="3" class="form-control" placeholder="Write Your Message*"></textarea>
                                </div>
                            </table>
                        </div>
                        <div class="form-group" style="text-align:center;margin:30px 0px">
                            <button type="submit" onclick="submitForm()" class="th-btn">Submit</button>
                        </div>
                    </div>
                    <div id="cheque-payment" style="display:none;">
                        <div class="text-center space-extra2" style="padding: 20px">
                            <h3 class="text-center" style="margin-bottom:30px;">Payment Details</h3>
                            <h6>Bank : ICICI </h6>
                            <h6>Name : INDIAN ASSOCIATION OF ORAL IMPLANTOLOGIETS</h6>
                            <h6>Branch : Saligramam,Chennai </h6>
                            <h6>IFSC Code : ICIC0001545</h6>
                            <h6>AC/No : 154505002510</h6>
                        </div>
                        <div class="col-md-10 aligncenter">
                            <table>
                                <div class="form-group">
                                    <label for="DD/Check_no">DD / Check no</label>
                                    <input type="text" id="DD/Check_no" name="DD/Check_no" placeholder="Enter Your DD/Check no" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="transaction_date">Dated Drawn On</label>
                                    <input type="date" id="transaction_date" name="transaction_date" class="form-control"><i class="fa fa-calendar-alt"></i>
                                </div>
                                <div class="form-group">
                                    <label for="imagefile">Choose a Payment Attachment</label>
                                    <input type="file" id="imagefile" name="imagefile" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="remark">Message</label>
                                    <textarea name="remark" id="remark" cols="30" rows="3" class="form-control" placeholder="Write Your Message*"></textarea>
                                </div>
                            </table>
                        </div>
                        <div class="form-group" style="text-align:center;margin:30px 0px">
                            <button type="submit" onclick="submitForm()" class="th-btn">Submit</button>
                        </div>
                    </div>
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
    const countryInput = document.getElementById('country');
    const defaultValue = 'India';

    countryInput.addEventListener('focus', function() {
        if (this.value === defaultValue) {
            this.value = '';
        }
    });

    countryInput.addEventListener('blur', function() {
        if (this.value.trim() === '') {
            this.value = defaultValue;
        }
    });

    function combineFirstName() {
        const title = document.getElementById('title').value;
        const firstName = document.getElementById('first_name_input').value;
        const firstNameField = document.getElementById('first_name');

        firstNameField.value = `${title} ${firstName}`;
    }

    function updateMobileNo() {
        const countryCode = document.getElementById('country_code').value;
        const mobileNumber = document.getElementById('mobile_no_input').value;
        const mobileNoField = document.getElementById('mobile_no');

        mobileNoField.value = `${countryCode}-${mobileNumber}`; // Correct use of template literals
    }

    document.getElementById('memberForm').addEventListener('submit', function(event) {
        combineFirstName();
        updateMobileNo();
        saveFormData(); // Ensure to call saveFormData here to save all form data
    });


    // Define the countries array once
const countries = [
    { code: '+1', name: 'United States' },
    { code: '+7', name: 'Russia' },
    { code: '+688', name: 'Tuvalu' },
    { code: '+689', name: 'French Polynesia' },
    { code: '+690', name: 'Tokelau' },
    { code: '+691', name: 'Micronesia' },
    { code: '+692', name: 'Marshall Islands' },
    { code: '+91', name: 'India' } // Added India here
];

function saveFormData() {
    const fields = ['first_name_input', 'last_name', 'dob', 'email_id', 'country_code', 'mobile_no_input', 
                    'college_name', 'field', 'reg_no', 'address', 'city', 'state', 'country', 'pincode', 'payment_mode'];
    const formData = {};
    
    fields.forEach(field => {
        const input = document.getElementById(field);
        if (input) {
            const key = field.replace('_input', ''); // Removing '_input' from the field name for the key
            formData[key] = input.value; // Storing the input value
        }
    });
    
    // Combine the country code and mobile number
    const countryCode = document.getElementById('country_code').value;
    const mobileNumber = document.getElementById('mobile_no_input').value;
    formData['mobile_no'] = `${countryCode}-${mobileNumber}`; // Save combined mobile number

    localStorage.setItem('formData', JSON.stringify(formData));
}

document.querySelectorAll('input, select').forEach(input => {
    input.addEventListener('input', saveFormData);
});

// Set initial values based on saved data
function prefillFormData() {
    const savedData = localStorage.getItem('formData');
    const defaultCountry = { code: '+91', name: 'India' };

    if (savedData) {
        const formData = JSON.parse(savedData);
        Object.keys(formData).forEach(key => {
            const input = document.getElementById(key === 'first_name' ? 'first_name_input' : key);
            if (input) {
                input.value = formData[key];
            }
        });

        // Set the country code and country name based on saved data
        const savedCountryCode = formData['country_code'];
        const countryInput = document.getElementById('country_code');
        const searchInput = document.getElementById('search_country_code');

        if (savedCountryCode) {
            const country = countries.find(c => c.code === savedCountryCode);
            if (country) {
                countryInput.value = country.code;
                searchInput.value = `${country.code} (${country.name})`; // Display correct country name
            } else {
                countryInput.value = defaultCountry.code;
                searchInput.value = `${defaultCountry.code} (${defaultCountry.name})`;
            }
        } else {
            countryInput.value = defaultCountry.code;
            searchInput.value = `${defaultCountry.code} (${defaultCountry.name})`;
        }
    }

    updateMobileNo();
}

// Call prefillFormData on window load
window.onload = prefillFormData;

document.getElementById('memberForm').addEventListener('submit', function(event) {
    combineFirstName();
    updateMobileNo();
    saveFormData(); // Call saveFormData to ensure all form data is saved
});

document.addEventListener('DOMContentLoaded', function() {
    const countryListContainer = document.getElementById('country_list');
    const searchInput = document.getElementById('search_country_code');
    const countryInput = document.getElementById('country_code');

    countryListContainer.style.display = 'none';

    // Set default country
    const defaultCountry = { code: '+91', name: 'India' };
    countryInput.value = defaultCountry.code;
    searchInput.value = `${defaultCountry.code} (${defaultCountry.name})`;

    function populateCountryList(countries) {
        countryListContainer.innerHTML = '';
        const limitedCountries = countries.slice(0, 5);
        limitedCountries.forEach(country => {
            const item = document.createElement('div');
            item.classList.add('country-item');
            item.textContent = `${country.code} (${country.name})`;
            item.dataset.code = country.code;
            item.addEventListener('click', function() {
                countryInput.value = this.dataset.code;
                searchInput.value = this.textContent;
                countryListContainer.style.display = 'none';
                updateMobileNo();
            });
            countryListContainer.appendChild(item);
        });

        countryListContainer.style.display = limitedCountries.length > 0 ? 'block' : 'none';
    }

    function showCountryList() {
        countryListContainer.style.display = 'block';
        const query = searchInput.value.toLowerCase();
        const filteredCountries = countries.filter(country =>
            country.name.toLowerCase().startsWith(query) || country.code.startsWith(query)
        );
        populateCountryList(filteredCountries);
    }

    searchInput.addEventListener('focus', function() {
        if (searchInput.value === `${defaultCountry.code} (${defaultCountry.name})`) {
            searchInput.value = '';
        }
        showCountryList();
    });

    searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase();
        const filteredCountries = countries.filter(country =>
            country.name.toLowerCase().startsWith(query) || country.code.startsWith(query)
        );
        populateCountryList(filteredCountries);
    });

    document.addEventListener('click', function(event) {
        if (!searchInput.contains(event.target) && !countryListContainer.contains(event.target)) {
            if (searchInput.value.trim() === '') {
                countryInput.value = defaultCountry.code;
                searchInput.value = `${defaultCountry.code} (${defaultCountry.name})`;
            }
            countryListContainer.style.display = 'none';
        }
    });
});


    document.getElementById('email_id').addEventListener('input', function() {
        var emailInput = this.value;
        var emailError = document.getElementById('email_error');

        var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!emailPattern.test(emailInput)) {
            emailError.style.display = 'block';
        } else {
            emailError.style.display = 'none';
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        const phpErrors = <?= json_encode($validation->getErrors()); ?>;

        if (phpErrors) {
            Object.keys(phpErrors).forEach(function(field) {
                const inputElement = document.getElementById(field);
                if (inputElement) {
                    const errorMessage = document.createElement('div');
                    errorMessage.classList.add('text-danger');
                    errorMessage.innerText = phpErrors[field];
                    inputElement.parentElement.appendChild(errorMessage);
                }
            });
        }
    });


    function submitForm(event) {
        event.preventDefault();

        const form = document.getElementById('memberForm');
        const paymentMode = document.getElementById('payment_mode').value;
        const errorMessage = document.getElementById('error-message');
        errorMessage.style.display = 'none';
        errorMessage.innerHTML = '';

        let allFilled = true;

        if (paymentMode === '2') {
            const requiredFields = ['DD/Check_no', 'transaction_date', 'imagefile', 'remark'];

            requiredFields.forEach(fieldId => {
                const input = document.getElementById(fieldId);
                if (!input.value) {
                    allFilled = false;
                    input.classList.add('is-invalid');
                } else {
                    input.classList.remove('is-invalid');
                }
            });

        } else if (paymentMode === '3') {
            const imagefile = document.getElementById('screenshotfile');
            const remark = document.getElementById('remarks');

            if (!imagefile.value || !remark.value.trim()) {
                allFilled = false;
                if (!imagefile.value) {
                    imagefile.classList.add('is-invalid');
                } else {
                    imagefile.classList.remove('is-invalid');
                }
                if (!remark.value.trim()) {
                    remark.classList.add('is-invalid');
                } else {
                    remark.classList.remove('is-invalid');
                }
            }
        }

        if (allFilled) {
            form.submit();
        } else {
            errorMessage.style.display = 'block';
            errorMessage.innerHTML = 'Please fill all the required fields.';
        }

        document.querySelectorAll('#memberForm input').forEach(input => {
            input.addEventListener('input', () => {
                if (input.value) {
                    input.classList.remove('is-invalid');
                }
            });
        });
    }


    document.getElementById('memberForm').addEventListener('submit', submitForm);

    function handlePaymentModeChange() {
        const paymentMode = document.getElementById('payment_mode').value;
        const paymentDetails = document.getElementById('payment-details');
        const chequePayment = document.getElementById('cheque-payment');
        const qrPayment = document.getElementById('Qr-payment');

        paymentDetails.style.display = 'block';

        if (paymentMode === '1') {
            chequePayment.style.display = 'none';
            qrPayment.style.display = 'none';
        } else if (paymentMode === '2') {
            chequePayment.style.display = 'block';
            qrPayment.style.display = 'none';
        } else if (paymentMode === '3') {
            chequePayment.style.display = 'none';
            qrPayment.style.display = 'block';
        }
    }
</script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>