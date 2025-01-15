// Validate amount to 5 digits and 2 decimals
function validateAmount(input) {
    let value = parseFloat(input.value);
    
    // Validate amount range
    if (!value ) {
        input.classList.add('is-invalid');
        if (!input.nextElementSibling || !input.nextElementSibling.classList.contains('invalid-feedback')) {
            const feedback = document.createElement('div');
            feedback.className = 'invalid-feedback d-block';
            input.parentNode.appendChild(feedback);
        }
    } else {
        input.classList.remove('is-invalid');
        const feedback = input.nextElementSibling;
        if (feedback && feedback.classList.contains('invalid-feedback')) {
            feedback.remove();
        }
    }
    
    
    
    calculateMonthlyPayment();
}

// Validate duration
function validateDuration(input) {
    let value = parseInt(input.value);
    
    // Validate duration range
    if (!value) {
        input.classList.add('is-invalid');
        if (!input.nextElementSibling || !input.nextElementSibling.classList.contains('invalid-feedback')) {
            const feedback = document.createElement('div');
            feedback.className = 'invalid-feedback d-block';
            input.parentNode.appendChild(feedback);
        }
    } else {
        input.classList.remove('is-invalid');
        const feedback = input.nextElementSibling;
        if (feedback && feedback.classList.contains('invalid-feedback')) {
            feedback.remove();
        }
    }
    
    
    calculateMonthlyPayment();
}

function calculateMonthlyPayment() {
    const amount = parseFloat(document.querySelector('[name="amount"]').value) || 0;
    const duration = parseInt(document.querySelector('[name="duration"]').value) || 1;
    
    if (amount&& duration) {
        // Formula: (Principal Amount / Duration) + (Principal Amount * 4.2% / Duration)
        const principal = amount / duration;
        const interest = (amount * 0.042) / duration;
        const monthly = principal + interest;
        document.querySelector('[name="monthly_payment"]').value = monthly.toFixed(2);
    } else {
        document.querySelector('[name="monthly_payment"]').value = '0.00';
    }
}

// Validate required fields in current step
function validateStep(step) {
    const currentStepContent = document.querySelector(`.step-content[data-step="${step}"]`);
    const requiredFields = currentStepContent.querySelectorAll('[required]');
    let isValid = true;

    requiredFields.forEach(field => {
        if (!field.value) {
            isValid = false;
            field.classList.add('is-invalid');
        } else {
            field.classList.remove('is-invalid');
        }

        // Special validation for loan type
        if (field.name === 'loan_type' && field.value === 'other') {
            const otherField = document.querySelector('[name="other_loan_type"]');
            if (!otherField.value) {
                isValid = false;
                otherField.classList.add('is-invalid');
            } else {
                otherField.classList.remove('is-invalid');
            }
        }

        // Special validation for amount
        if (field.name === 'amount') {
            const amount = parseFloat(field.value);
            if (!amount) {
                isValid = false;
                field.classList.add('is-invalid');
            }
        }
    });

    return isValid;
}

// Initialize form wizard and validation
document.addEventListener('DOMContentLoaded', function() {
    // Show/hide other loan type field
    document.querySelector('[name="loan_type"]').addEventListener('change', function() {
        const otherField = document.getElementById('otherLoanType');
        otherField.style.display = this.value === 'other' ? 'block' : 'none';
    });

    // Add event listeners for real-time calculation
    const amountInput = document.querySelector('[name="amount"]');
    const durationInput = document.querySelector('[name="duration"]');
    
    amountInput.addEventListener('keyup', calculateMonthlyPayment);
    amountInput.addEventListener('change', calculateMonthlyPayment);
    durationInput.addEventListener('keyup', calculateMonthlyPayment);
    durationInput.addEventListener('change', calculateMonthlyPayment);
    
    // Initial calculation
    calculateMonthlyPayment();

    // Form wizard initialization
    const form = document.querySelector('form');
    const stepContents = document.querySelectorAll('.step-content');
    const stepIndicators = document.querySelectorAll('.step-indicator .step');
    const prevButton = document.querySelector('.prev-step');
    const nextButton = document.querySelector('.next-step');
    const submitButton = document.querySelector('.submit-form');
    let currentStep = 1;

    function updateSteps() {
        stepContents.forEach(content => content.classList.remove('active'));
        document.querySelector(`.step-content[data-step="${currentStep}"]`).classList.add('active');
        
        stepIndicators.forEach((indicator, index) => {
            if (index + 1 < currentStep) {
                indicator.classList.add('completed');
                indicator.classList.remove('active');
            } else if (index + 1 === currentStep) {
                indicator.classList.add('active');
                indicator.classList.remove('completed');
            } else {
                indicator.classList.remove('completed', 'active');
            }
        });

        prevButton.style.display = currentStep === 1 ? 'none' : 'inline-block';
        nextButton.style.display = currentStep === 4 ? 'none' : 'inline-block';
        submitButton.style.display = currentStep === 4 ? 'inline-block' : 'none';
    }

    nextButton.addEventListener('click', () => {
        if (currentStep < 5 && validateStep(currentStep)) {
            currentStep++;
            updateSteps();
        } else if (!validateStep(currentStep)) {
            alert('Sila lengkapkan semua maklumat yang diperlukan sebelum meneruskan.');
            return false;
        }
    });

    prevButton.addEventListener('click', () => {
        if (currentStep > 1) {
            currentStep--;
            updateSteps();
        }
    });

    // Confirmation checkbox handling
    const confirmationCheckbox = document.getElementById('confirmationCheckbox');
    confirmationCheckbox.addEventListener('change', function() {
        nextButton.disabled = !this.checked;
    });

    // Form submission handler
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validate all steps before submission
        let isValid = true;
        for (let step = 1; step <= 4; step++) {
            if (!validateStep(step)) {
                isValid = false;
                currentStep = step;
                updateSteps();
                alert('Sila lengkapkan semua maklumat yang diperlukan di Bahagian ' + step);
                break;
            }
        }

        if (isValid) {
            // Check if declaration is confirmed
            const declarationCheckbox = document.querySelector('[name="declaration_confirmed"]');
            if (!declarationCheckbox.checked) {
                alert('Sila sahkan pengakuan sebelum menghantar permohonan');
                return false;
            }

            // Submit the form
            this.submit();
        }
    });
}); 