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


<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>