document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('membershipForm');
    const steps = document.querySelectorAll('.step');
    const contents = document.querySelectorAll('.step-content');
    const prevBtn = document.querySelector('.prev-step');
    const nextBtn = document.querySelector('.next-step');
    const submitBtn = document.querySelector('.submit-form');
    let currentStep = 1;
    let maxStepReached = 1;

    function updateStep(step) {
        // Update step indicators
        steps.forEach((s, index) => {
            s.classList.remove('active');
            // Mark all steps up to maxStepReached as completed, except current step
            if (index + 1 <= maxStepReached && index + 1 !== step) {
                s.classList.add('completed');
            } else {
                s.classList.remove('completed');
            }
        });
        steps[step-1].classList.add('active');

        // Update step content
        contents.forEach(c => c.classList.remove('active'));
        contents[step-1].classList.add('active');

        // Update buttons
        prevBtn.style.display = step === 1 ? 'none' : 'block';
        nextBtn.style.display = step === steps.length ? 'none' : 'block';
        submitBtn.style.display = step === steps.length ? 'block' : 'none';
    }

    // Add click event listeners to steps
    steps.forEach((step, index) => {
        step.addEventListener('click', () => {
            const stepNumber = index + 1;
            // Only allow clicking on completed steps or the next available step
            if (stepNumber <= maxStepReached) {
                currentStep = stepNumber;
                updateStep(currentStep);
            }
        });
    });

    nextBtn.addEventListener('click', () => {
        if (currentStep < steps.length) {
            currentStep++;
            maxStepReached = Math.max(maxStepReached, currentStep);
            updateStep(currentStep);
        }
    });

    prevBtn.addEventListener('click', () => {
        if (currentStep > 1) {
            currentStep--;
            updateStep(currentStep);
        }
    });
}); 