function updateFields() {
  let category = document.getElementById("category").value;
  const inputFields = document.getElementById("inputFields");
  inputFields.innerHTML = "";

  const commonFields = `
        <div class="input-container">
            <label for="first_name">First Name*</label>
            <input type="text" id="first_name" required placeholder="Enter Your First Name">
        </div>

        <div class="input-container">
            <label for="last_name">Last Name</label>
            <input type="text" id="last_name" placeholder="Enter Your Last Name">
        </div>

        <div class="input-container">
            <label for="email">Email Address*</label>
            <input type="email" id="email" required placeholder="Enter Your Email Address">
            </div>
            <p class="error" id="email_error-message" style="text-align: center;display:none">Enter a valid Email Id</p>

        <div class="input-container">
            <label for="phone">Phone Number*</label>
            <div class="phone-input-container">
                <select id="country_code" required onchange="updateMobileNo()">
                    <option value="+91">India (+91)</option>
                    <option value="+94">Sri Lanka (+94)</option>
                    <option value="+971">UAE (+971)</option>
                    <option value="+968">Oman (+968)</option>
                    <option value="+66">Thailand (+66)</option>
                    <option value="+39">Italy (+39)</option>
                    <option value="+84">Vietnam (+84)</option>
                    <option value="+964">Iraq (+964)</option>
                    <option value="+855">Cambodia (+855)</option>
                    <option value="+60">Malaysia (+60)</option>
                    <option value="+960">Maldives (+960)</option>
                    <option value="+1">USA (+1)</option>
                </select>
                <input type="tel" id="phone" required placeholder="Enter Your Phone Number" maxlength="15" oninput="updateMobileNo()">
            </div>
        </div>
            <p class="error" id="phone_error-message" style="text-align: end; display: none;">
                Please enter a valid mobile number
            </p>


        <input type="hidden" id="mobile_no" name="mobile_no">

        <div class="input-container">
            <label for="address">Address*</label>
            <input id="address" required placeholder="Enter Your Address"></input>
        </div>

        <div class="input-container">
            <label for="state">State</label>
            <input type="text" id="state" placeholder="Enter Your State">
        </div>

        <div class="input-container">
            <label for="country">Country</label>
            <input type="text" id="country" value="india">
        </div>
    `;

  const fields = {
    "IAOI MEMBERS": `
            <div class="input-container">
                <label for="member_id">Member ID*</label>
                <input type="text" id="member_id" required placeholder="Enter Member ID" onblur="fetchMemberDetails()">
            </div>
            <p class="error" id="error-message" style="text-align: end;display:none">Member not found. Please check the Member ID.</p>
        `,

    "AFFILIATE MEMBERS": `
            <div class="input-container">
                <label for="association_id">Association ID</label>
                <input type="text" id="association_id" placeholder="Enter Association ID">
            </div>
            <div class="input-container">
                <label for="association_name">Association Name</label>
                <input type="text" id="association_name" required placeholder="Enter Your Association Name">
            </div>
        `,

    "COMBO - NEW MEMBER + CONFERENCE": `
            <div class="input-container">
                <label for="dci-reg-no">DCI Reg No</label>
                <input type="text" id="dci-reg-no" required placeholder="Enter DCI Reg No">
            </div>
        `,

    "GROUP REGISTRATIONS": `
        <div class="input-container">
                <label for="member_id">Member ID*</label>
                <input type="text" id="member_id" required placeholder="Enter Member ID" onblur="fetchMemberDetails()">
            </div>
            <p class="error" id="error-message" style="text-align: end;display:none">Member not found. Please check the Member ID.</p>
        `,

    "INTERNATIONAL DELEGATES": "",
  };
  // First, insert category-specific fields (above common fields)
  if (fields[category]) {
    inputFields.innerHTML = fields[category];
  }

  // Then insert common fields
  inputFields.insertAdjacentHTML("beforeend", commonFields);

  // If "GROUP REGISTRATIONS" is selected, insert "Member IDs" fields below common fields
  if (category === "GROUP REGISTRATIONS") {
    const groupFields = ` 
            <div class="input-container" style="margin-bottom: 0px;">
                <label for="member_ids">Member IDs (click "+" to add)</label>
                <div class="input-with-button">
                    <input type="text" id="member_ids" placeholder="Enter Member ID">
                    <button type="button" id="add_id_button">+</button>
                </div>
            </div>
            <div id="verification_results" class="verification"></div>
            <p class="error" id="memberIds-error-message" style="text-align: center;display:none">Please enter Atleast</p>
        `;

    inputFields.insertAdjacentHTML("beforeend", groupFields);
    setTimeout(initScript, 100);
  }
}

function initScript() {
  const addIdButton = document.getElementById("add_id_button");
  const memberIdInput = document.getElementById("member_ids");
  const resultsContainer = document.getElementById("verification_results");
  const registerButton = document.getElementById("click_to_register");
  const errorMessage = document.getElementById("memberIds-error-message");

  if (
    !addIdButton ||
    !memberIdInput ||
    !resultsContainer ||
    !registerButton ||
    !errorMessage
  ) {
    console.error("One or more required elements are missing.");
    return;
  }

  addIdButton.addEventListener("click", function () {
    addMemberId();
  });

  memberIdInput.addEventListener("keypress", function (event) {
    if (event.key === "Enter") {
      event.preventDefault();
      addMemberId();
    }
  });

  registerButton.addEventListener("click", function (event) {
    checkMemberCount(true); // Force error check when submitting
  });

  function addMemberId() {
    const memberId = memberIdInput.value.trim();
    if (memberId === "") return;

    // Prevent duplicate entries
    if (document.querySelector(`.tag[data-id="${memberId}"]`)) {
      errorMessage.style.display = "block";
      errorMessage.textContent = "Duplicate Member ID entered!";
      return;
    }

    errorMessage.style.display = "none"; // Hide error message if valid

    // Fetch member details from API
    fetch(`https://iaoi.in/api/v1/member/findMember/${memberId}`)
      .then((response) => response.json())
      .then((data) => {
        const tag = document.createElement("div");
        tag.className = "tag";
        tag.setAttribute("data-id", memberId); // Store ID in attribute

        if (data.statusCode === 200 && data.result.length > 0) {
          const member = data.result[0];
          tag.classList.add("verified");
          tag.innerHTML = `<strong>${memberId}</strong>: ${member.fullName} 
                                     <span class="delete" onclick="removeTag(this)">x</span>`;
        } else {
          tag.classList.add("not-found");
          tag.innerHTML = `<strong>${memberId}</strong>: Not a Member 
                                     <span class="delete" onclick="removeTag(this)">x</span>`;
        }
        resultsContainer.appendChild(tag);

        checkMemberCount(); // Recalculate after adding
      })
      .catch(() => {
        memberIdInput.value = "";
        memberIdInput.placeholder = "Error fetching details";
        memberIdInput.classList.add("error");
      });

    memberIdInput.value = ""; // Clear input field
  }

  // Function to remove a tag
  function removeTag(element) {
    element.parentElement.remove();
    checkMemberCount();
  }

  // Update the checkMemberCount function
  function checkMemberCount() {
    const memberTags = resultsContainer.querySelectorAll(".tag");
    const verifiedTags = resultsContainer.querySelectorAll(".tag.verified"); // Count verified members
    const existingIds = memberTags.length;
    const verifiedCount = verifiedTags.length;

    if (existingIds < 9) {
      errorMessage.style.display = "block";
      errorMessage.textContent = "Please enter at least 9 Member IDs.";
      registerButton.setAttribute("disabled", "true");
      registerButton.classList.add("disabled-style");
    } else if (verifiedCount < 9) {
      errorMessage.style.display = "block";
      errorMessage.textContent = "All Member IDs must be verified.";
      registerButton.setAttribute("disabled", "true");
      registerButton.classList.add("disabled-style");
    } else {
      errorMessage.style.display = "none";
      registerButton.removeAttribute("disabled");
      registerButton.classList.remove("disabled-style");
    }
  }
}

function handleKeyPress(event) {
  if (event.key === "Enter") {
    const memberIdInput = document.getElementById("member_ids");
    const memberId = memberIdInput.value.trim();
    if (memberId) {
      verifyMemberId(memberId);
      memberIdInput.value = "";
    }
  }
}
function updateMobileNo() {
  const countryCode = document.getElementById("country_code").value;
  const phoneNumber = document.getElementById("phone").value;
  const mobileNo = countryCode + phoneNumber;
  document.getElementById("mobile_no").value = mobileNo;
}
function verifyMemberId(memberId) {
  const resultsContainer = document.getElementById("verification_results");
  fetch(`https://iaoi.in/api/v1/member/findMember/${memberId}`)
    .then((response) => response.json())
    .then((data) => {
      const tag = document.createElement("div");
      tag.className = "tag";
      if (data.statusCode === 200 && data.result.length > 0) {
        const member = data.result[0];
        tag.classList.add("verified");
        tag.innerHTML = `<strong>${memberId}</strong>: ${member.fullName} <span class="delete" onclick="removeTag(this)">x</span>`;
      } else {
        tag.classList.add("not-found");
        tag.innerHTML = `<strong>${memberId}</strong>: Not a Member <span class="delete" onclick="removeTag(this)">x</span>`;
      }
      resultsContainer.appendChild(tag);
    })
    .catch(() => {
      const memberIdInput = document.getElementById("member_ids");
      memberIdInput.value = "";
      memberIdInput.placeholder = "Error fetching details";
      memberIdInput.classList.add("error");
    });
}

function removeTag(tagElement) {
  const tag = tagElement.parentElement;
  tag.remove();
}
function fetchMemberDetails() {
  const memberId = document.getElementById("member_id").value.trim();
  const submitButton = document.getElementById("click_to_register");
  const errorMessage = document.getElementById("error-message");

  if (!memberId) return;

  fetch(`https://iaoi.in/api/v1/member/findMember/${memberId}`)
    .then((response) => response.json())
    .then((data) => {
      if (data.statusCode === 200 && data.result.length > 0) {
        const member = data.result[0];
        document.getElementById("first_name").value = member.first_name || "";
        document.getElementById("last_name").value = member.last_name || "";
        document.getElementById("email").value = member.email_id || "";
        document.getElementById("country").value = member.country || "";
        document.getElementById("phone").value = (
          member.mobile_no || ""
        ).replace(/^\+91-/, "");
        document.getElementById("address").value = member.address || "";
        document.getElementById("state").value = member.state || "";
        errorMessage.style.display = "none";
        submitButton.disabled = false;
      } else {
        errorMessage.style.display = "block";
        submitButton.disabled = true;
      }
    })
    .catch(() => (errorMessage.style.display = "block"));
  submitButton.disabled = true;
}
document.addEventListener("DOMContentLoaded", function () {
  document.getElementById("step0").style.display = "block";
  document.getElementById("step1").style.display = "none";
  document.getElementById("step2").style.display = "none";
});
function handleMembershipChoice(choice) {
  document.getElementById("step0").style.display = "none";
  document.getElementById("step1").style.display = "block";
  let categorySelect = document.getElementById("category");
  categorySelect.innerHTML = "";

  if (choice === "yes") {
    let iaoiOption = new Option("IAOI Members", "IAOI MEMBERS");
    let groupOption = new Option(
      "Group Registration (Min 10 Members)",
      "GROUP REGISTRATIONS"
    );
    categorySelect.appendChild(iaoiOption);
    categorySelect.appendChild(groupOption);
    categorySelect.value = "IAOI MEMBERS";
  } else {
    categorySelect.innerHTML = `
        // <option value="COMBO - NEW MEMBER + CONFERENCE" selected>Combo - New Member + Conference</option>
        // <option value="AFFILIATE MEMBERS">Affiliate Association Members</option>
        `;
  }
  updateFields();
}
async function nextStep() {
  const category = document.getElementById("category").value;
  const email_id = document.getElementById("email").value.trim() ?? "";
  const first_name = document.getElementById("first_name").value.trim() ?? "";
  const last_name = document.getElementById("last_name").value.trim() ?? "";
  const address = document.getElementById("address").value.trim() ?? "";
  const state = document.getElementById("state").value.trim() ?? "";
  const country = document.getElementById("country").value.trim() ?? "";
  const countryCode = document.getElementById("country_code").value;
  const phoneNumber = document.getElementById("phone").value;
  const mobile_no = countryCode + phoneNumber;
  let member_id = "";
  let reg_no = "";
  let association_id = "";
  let association_name = "";
  let member_ids = "";
  let memberIdCount = 0;

  if (category === "AFFILIATE MEMBERS") {
    association_id =
      document.getElementById("association_id").value.trim() ?? "";
    association_name =
      document.getElementById("association_name").value.trim() ?? "";
  } else if (category === "COMBO - NEW MEMBER + CONFERENCE") {
    reg_no = document.getElementById("dci-reg-no").value.trim() ?? "";
  } else if (category === "IAOI MEMBERS") {
    member_id = document.getElementById("member_id").value.trim() ?? "";
  } else if (category === "GROUP REGISTRATIONS") {
    member_id = document.getElementById("member_id").value.trim() ?? "";
    const memberIdsContainer = document.getElementById("verification_results");
    const memberTags = memberIdsContainer.querySelectorAll(".tag"); // Get all member tags
    const verifiedTags = memberIdsContainer.querySelectorAll(".tag.verified"); // Only verified members

    memberIdCount = verifiedTags.length + 1;

    console.log("Verified MemberID COUNT =", memberIdCount);
    console.log("Verified MemberID LIST =", verifiedTags);

    if (memberTags.length < 9) {
      const errorMessage = document.getElementById("memberIds-error-message");
      errorMessage.style.display = "block";
      errorMessage.textContent = "Please enter at least 9 Member IDs.";
      return;
    } else if (memberIdCount < 9) {
      const errorMessage = document.getElementById("memberIds-error-message");
      errorMessage.style.display = "block";
      errorMessage.textContent = "All Member IDs must be verified.";
      return;
    } else {
      document.getElementById("memberIds-error-message").style.display = "none";
    }

    // Collect verified member IDs
    member_ids = Array.from(verifiedTags)
      .map((tag) => tag.getAttribute("data-id"))
      .join(",");
  }

  // Validate required fields
  const inputs = document.querySelectorAll("input[required], select[required]");
  let allFilled = true;
  let firstErrorField = null;

  // Email validation regex
  const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  const emailErrorMessage = document.getElementById("email_error-message");
  const phoneErrorMessage = document.getElementById("phone_error-message");

  inputs.forEach((input) => {
    const value = input.value.trim();

    if (input.type === "email") {
      emailErrorMessage.style.display = emailPattern.test(value)
        ? "none"
        : "block";
      input.classList.toggle("error", !emailPattern.test(value));
      allFilled = allFilled && emailPattern.test(value);
      firstErrorField =
        firstErrorField || (!emailPattern.test(value) ? input : null);
    }

    if (input.id === "phone") {
      const isValidPhone = value.length >= 8;
      phoneErrorMessage.style.display = isValidPhone ? "none" : "block";
      input.classList.toggle("error", !isValidPhone);
      allFilled = allFilled && isValidPhone;
      firstErrorField = firstErrorField || (!isValidPhone ? input : null);
    }

    const isEmpty = value === "";
    input.classList.toggle("error", isEmpty);
    allFilled = allFilled && !isEmpty;
    firstErrorField = firstErrorField || (isEmpty ? input : null);

    input.addEventListener(
      "focus",
      () => (input.style.backgroundColor = "#dd36360f")
    );
    input.addEventListener("input", () => {
      input.classList.remove("error");
      input.style.backgroundColor = "";
      if (input.type === "email") emailErrorMessage.style.display = "none";
      if (input.id === "phone") phoneErrorMessage.style.display = "none";
    });
  });

  if (firstErrorField) {
    firstErrorField.focus();
    firstErrorField.click();
    return;
  }

  if (!category) {
    alert("Please select a plan first!");
    return;
  }

  if (!allFilled) {
    event.preventDefault();
    return;
  }

  // Proceed to the next step
  document.getElementById("step1").style.display = "none";
  document.getElementById("step2").style.display = "block";
  document.getElementById("selected_plan").innerText = category;

  const todayTimestamp = new Date().getTime();
  let planAmount = 0;

  const cutoffDates = {
    "IAOI MEMBERS": [
      new Date("2025-04-15"),
      new Date("2025-05-30"),
      new Date("2025-07-31"),
    ],
    "AFFILIATE MEMBERS": [
      new Date("2025-04-15"),
      new Date("2025-05-30"),
      new Date("2025-07-31"),
    ],
    "COMBO - NEW MEMBER + CONFERENCE": [new Date("2025-05-30")],
    "GROUP REGISTRATIONS": [new Date("2025-05-30")],
    "INTERNATIONAL DELEGATES": [
      new Date("2025-04-15"),
      new Date("2025-05-30"),
      new Date("2025-07-31"),
    ],
  };

  // Function to fetch exchange rate
  async function getExchangeRate() {
    try {
      const response = await fetch(
        "https://api.exchangerate-api.com/v4/latest/USD"
      );
      const data = await response.json();
      return data.rates.INR;
    } catch (error) {
      console.error("Error fetching exchange rate:", error);
      return 83; // Default to 83 in case of error
    }
  }

  let usdToInr = await getExchangeRate();

  // Determine plan amount based on category and cutoff dates
  switch (category) {
    case "IAOI MEMBERS":
      planAmount =
        todayTimestamp <= cutoffDates[category][0].getTime()
          ? 6500
          : todayTimestamp <= cutoffDates[category][1].getTime()
          ? 7500
          : 10000;
      break;
    case "AFFILIATE MEMBERS":
      planAmount =
        todayTimestamp <= cutoffDates[category][0].getTime()
          ? 8500
          : todayTimestamp <= cutoffDates[category][1].getTime()
          ? 9000
          : 9900;
      break;
    case "COMBO - NEW MEMBER + CONFERENCE":
      planAmount =
        todayTimestamp <= cutoffDates[category][0].getTime() ? 11000 : 0;
      break;
    case "GROUP REGISTRATIONS":
      planAmount =
        todayTimestamp <= cutoffDates[category][0].getTime()
          ? 6000 * memberIdCount
          : 0;
      break;
    case "INTERNATIONAL DELEGATES":
      const usdAmount =
        todayTimestamp <= cutoffDates[category][1].getTime() ? 175 : 200;
      planAmount = usdAmount * usdToInr;
      break;
  }

  // Calculate GST and total amount
  const gstAmount = planAmount * 0.18;
  const totalAmount = planAmount + gstAmount;

  document.getElementById("plan_amount").innerText = `₹ ${planAmount.toFixed(
    2
  )}`;
  document.getElementById("gst_amount").innerText = `₹ ${gstAmount.toFixed(2)}`;
  document.getElementById("total_amount").innerText = `₹ ${totalAmount.toFixed(
    2
  )}`;
  document.getElementById("hidden_total_amount").value = totalAmount.toFixed(2);
  let plan_id = 0;
  switch (category) {
    case "IAOI MEMBERS":
      plan_id = 1;
      break;
    case "AFFILIATE MEMBERS":
      plan_id = 2;
      break;
    case "COMBO - NEW MEMBER + CONFERENCE":
      plan_id = 3;
      break;
    case "GROUP REGISTRATIONS":
      plan_id = 4;
      break;
    case "INTERNATIONAL DELEGATES":
      plan_id = 5;
      break;
  }
  document.getElementById("hidden_plan_id").value = plan_id;
  const payload = {
    category,
    email_id,
    last_name,
    first_name,
    mobile_no,
    address,
    state,
    country,
    member_id,
    reg_no,
    association_id,
    association_name,
    member_ids,
  };

  document.getElementById("payload").value = JSON.stringify(payload, null, 2);
}

function prevStep() {
  event.preventDefault();
  if (document.getElementById("step1").style.display === "block") {
    document.getElementById("step1").style.display = "none";
    document.getElementById("step0").style.display = "block";
  } else {
    document.getElementById("step2").style.display = "none";
    document.getElementById("step1").style.display = "block";
  }
}
