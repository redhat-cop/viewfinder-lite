/**
 * Digital Sovereignty Readiness Assessment - Interactive Features
 * Provides auto-save, score tracking, and UX enhancements
 */

$(document).ready(function() {

    // ==========================================
    // SECTION NAVIGATION
    // ==========================================

    let currentSection = 1;
    const totalSections = 7;

    /**
     * Show a specific section and hide others
     */
    function showSection(sectionNumber) {
        // Hide all sections
        $('.section-pane').hide();

        // Show the requested section
        $('.section-pane[data-section="' + sectionNumber + '"]').fadeIn(300);

        // Update current section
        currentSection = sectionNumber;

        // Update progress indicator
        $('#current-section').text(currentSection);
        const progressPercentage = (currentSection / totalSections) * 100;
        $('#section-progress-bar').css('width', progressPercentage + '%');

        // Show/hide intro section (only show on first section)
        if (currentSection === 1) {
            $('#intro-section').slideDown(300);
        } else {
            $('#intro-section').slideUp(300);
        }

        // Update navigation buttons
        updateNavigationButtons();

        // Scroll to top of form
        $('html, body').animate({
            scrollTop: $('.qualifier-header').offset().top - 100
        }, 300);
    }

    /**
     * Update visibility of navigation buttons
     */
    function updateNavigationButtons() {
        // Previous button: hide on first section
        if (currentSection === 1) {
            $('#prev-section').hide();
        } else {
            $('#prev-section').show();
        }

        // Next button: hide on last section
        if (currentSection === totalSections) {
            $('#next-section').hide();
            $('#submit-form').show();
        } else {
            $('#next-section').show();
            $('#submit-form').hide();
        }
    }

    /**
     * Validate current section has all questions answered
     */
    function validateCurrentSection() {
        const currentPane = $('.section-pane[data-section="' + currentSection + '"]');
        const totalQuestions = currentPane.find('.button-group').length;
        const answeredQuestions = currentPane.find('input[type="radio"]:checked').length;

        if (answeredQuestions < totalQuestions) {
            const unanswered = totalQuestions - answeredQuestions;
            alert('Please answer all questions before proceeding.\n\n' +
                  'You have ' + unanswered + ' unanswered question(s) in this section.');
            return false;
        }
        return true;
    }

    /**
     * Navigate to next section
     */
    function nextSection() {
        // Validate all questions are answered
        if (!validateCurrentSection()) {
            return;
        }

        if (currentSection < totalSections) {
            showSection(currentSection + 1);
            saveProgress(); // Auto-save when navigating
        }
    }

    /**
     * Navigate to previous section
     */
    function prevSection() {
        if (currentSection > 1) {
            showSection(currentSection - 1);
            saveProgress(); // Auto-save when navigating
        }
    }

    // Navigation button click handlers
    $('#next-section').on('click', function() {
        nextSection();
    });

    $('#prev-section').on('click', function() {
        prevSection();
    });

    // Keyboard navigation
    $(document).on('keydown', function(e) {
        // Arrow right or Enter (when not on submit button) = Next
        if (e.key === 'ArrowRight' && currentSection < totalSections) {
            e.preventDefault();
            nextSection();
        }
        // Arrow left = Previous
        else if (e.key === 'ArrowLeft' && currentSection > 1) {
            e.preventDefault();
            prevSection();
        }
    });


    // ==========================================
    // AUTO-SAVE FUNCTIONALITY
    // ==========================================

    /**
     * Save form progress to localStorage on any radio button change
     */
    function saveProgress() {
        const formData = {};

        // Save current section
        formData.currentSection = currentSection;

        // Save radio button selections
        $('input[type="radio"]:checked').each(function() {
            formData[this.name] = this.value;
        });

        localStorage.setItem('ds-qualifier-progress', JSON.stringify(formData));
        console.log('Progress saved to localStorage');
    }

    /**
     * Restore form progress from localStorage
     */
    function restoreProgress() {
        const saved = localStorage.getItem('ds-qualifier-progress');

        if (saved) {
            try {
                const formData = JSON.parse(saved);

                // Restore radio button selections
                for (const name in formData) {
                    if (name !== 'currentSection') {
                        const value = formData[name];
                        $('input[name="' + name + '"][value="' + value + '"]').prop('checked', true);
                    }
                }

                // Restore current section
                if (formData.currentSection && formData.currentSection >= 1 && formData.currentSection <= totalSections) {
                    showSection(formData.currentSection);
                }

                // Update scores after restoration
                updateScores();

                // Show notification
                showNotification('Previous progress restored!', 'success');

            } catch (e) {
                console.error('Error restoring progress:', e);
            }
        }
    }

    /**
     * Clear saved progress from localStorage
     */
    function clearProgress() {
        localStorage.removeItem('ds-qualifier-progress');
        console.log('Progress cleared from localStorage');
    }


    // ==========================================
    // SCORE TRACKING
    // ==========================================

    /**
     * Update running score counters (scores now calculated on results page)
     */
    function updateScores() {
        // Score display removed - scores are now calculated only on results page
        // This function is kept to avoid breaking existing calls
    }


    // ==========================================
    // NOTIFICATION SYSTEM
    // ==========================================

    /**
     * Show temporary notification message
     */
    function showNotification(message, type = 'info') {
        // Remove existing notifications
        $('.ds-notification').remove();

        // Create notification element
        const notification = $('<div>')
            .addClass('ds-notification ds-notification-' + type)
            .html('<i class="fa-solid fa-circle-check"></i> ' + message)
            .hide();

        // Add to page
        $('body').prepend(notification);

        // Animate in
        notification.slideDown(300);

        // Auto-hide after 3 seconds
        setTimeout(function() {
            notification.slideUp(300, function() {
                $(this).remove();
            });
        }, 3000);
    }


    // ==========================================
    // FORM VALIDATION
    // ==========================================

    /**
     * Enhanced form validation before submit
     */
    function validateForm() {
        const answeredCount = $('input[type="radio"]:checked').length;

        if (answeredCount === 0) {
            const confirmed = confirm(
                'You haven\'t answered any questions. This will result in a score of 0.\n\n' +
                'Are you sure you want to continue?'
            );
            return confirmed;
        }

        return true;
    }


    // ==========================================
    // SMOOTH SCROLLING
    // ==========================================

    /**
     * Smooth scroll to domain sections
     */
    function setupSmoothScroll() {
        $('a[href^="#domain-"]').on('click', function(e) {
            e.preventDefault();

            const target = $(this.getAttribute('href'));
            if (target.length) {
                $('html, body').animate({
                    scrollTop: target.offset().top - 100
                }, 500);
            }
        });
    }


    // ==========================================
    // KEYBOARD SHORTCUTS
    // ==========================================

    /**
     * Add keyboard shortcuts for power users
     */
    function setupKeyboardShortcuts() {
        $(document).on('keydown', function(e) {
            // Ctrl/Cmd + S to save progress manually
            if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                e.preventDefault();
                saveProgress();
                showNotification('Progress saved!', 'success');
            }

            // Ctrl/Cmd + Enter to submit form
            if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
                e.preventDefault();
                if (validateForm()) {
                    $('#qualifier-form').submit();
                }
            }
        });
    }


    // ==========================================
    // PROGRESS INDICATOR
    // ==========================================

    /**
     * Show completion percentage
     */
    function updateProgressIndicator() {
        const totalQuestions = $('.button-group').length;
        const answeredQuestions = $('.button-group').filter(function() {
            return $(this).find('input[type="radio"]:checked').length > 0;
        }).length;
        const percentage = Math.round((answeredQuestions / totalQuestions) * 100);

        // Create or update progress indicator
        let progressBar = $('#progress-indicator');
        if (progressBar.length === 0) {
            progressBar = $('<div>')
                .attr('id', 'progress-indicator')
                .html('<div class="progress-text"></div><div class="progress-bar-mini"></div>');
            $('.score-preview').append(progressBar);
        }

        progressBar.find('.progress-text').text(percentage + '% Complete');
        progressBar.find('.progress-bar-mini').css('width', percentage + '%');
    }


    // ==========================================
    // EVENT LISTENERS
    // ==========================================

    // Listen for radio button changes
    $('.question-radio').on('change', function() {
        updateScores();
        updateProgressIndicator();
        saveProgress();

        // Visual feedback on parent container
        const questionItem = $(this).closest('.question-item');
        const hasAnswer = questionItem.find('input[type="radio"]:checked').length > 0;

        if (hasAnswer) {
            questionItem.addClass('answered');
        } else {
            questionItem.removeClass('answered');
        }
    });

    // Form submit validation
    $('#qualifier-form').on('submit', function(e) {
        if (!validateForm()) {
            e.preventDefault();
            return false;
        }

        // Clear saved progress on successful submit
        clearProgress();
    });

    // Form reset handler
    $('#qualifier-form').on('reset', function() {
        setTimeout(function() {
            updateScores();
            updateProgressIndicator();
            clearProgress();
            showSection(1); // Return to first section
            showNotification('Form reset', 'info');
        }, 50);
    });


    // ==========================================
    // INITIALIZATION
    // ==========================================

    /**
     * Initialize all features on page load
     */
    function init() {
        console.log('DS Qualifier JavaScript initialized');

        // Initialize navigation buttons
        updateNavigationButtons();

        // Restore any saved progress (which may change the section)
        restoreProgress();

        // Setup features
        setupSmoothScroll();
        setupKeyboardShortcuts();
        updateProgressIndicator();

        // Initial score calculation (in case of restored data)
        updateScores();

        // Add helpful tooltip
        $('.score-preview').attr('title', 'Keyboard shortcuts: Arrow keys to navigate, Ctrl+S to save');

        // Add visual styles for notifications
        addNotificationStyles();

        console.log('All features loaded successfully');
    }

    /**
     * Inject notification styles dynamically
     */
    function addNotificationStyles() {
        if ($('#ds-notification-styles').length === 0) {
            const styles = `
                <style id="ds-notification-styles">
                    .ds-notification {
                        position: fixed;
                        top: 20px;
                        right: 20px;
                        padding: 1rem 1.5rem;
                        background: #2a2a2a;
                        border-radius: 4px;
                        color: #fff;
                        box-shadow: 0 4px 12px rgba(0,0,0,0.4);
                        z-index: 9999;
                        font-size: 1rem;
                    }
                    .ds-notification-success {
                        border-left: 4px solid #2aaa04;
                    }
                    .ds-notification-error {
                        border-left: 4px solid #c9190b;
                    }
                    .ds-notification-info {
                        border-left: 4px solid #0d60f8;
                    }
                    .question-item.answered {
                        background: #252525;
                    }
                    #progress-indicator {
                        margin-top: 1rem;
                        text-align: center;
                    }
                    .progress-text {
                        color: #9ec7fc;
                        font-size: 0.9rem;
                        margin-bottom: 0.5rem;
                    }
                    .progress-bar-mini {
                        height: 6px;
                        background: #0d60f8;
                        border-radius: 3px;
                        transition: width 0.3s ease;
                    }
                </style>
            `;
            $('head').append(styles);
        }
    }


    // Run initialization
    init();


    // ==========================================
    // EXPOSE PUBLIC API (for debugging)
    // ==========================================

    window.DSQualifier = {
        saveProgress: saveProgress,
        restoreProgress: restoreProgress,
        clearProgress: clearProgress,
        updateScores: updateScores,
        showNotification: showNotification
    };

});
