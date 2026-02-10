<!doctype html>
<html lang="en-us" class="pf-theme-dark">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Viewfinder Lite</title>
  <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/brands.css" />
  <link rel="stylesheet" href="css/style.css" />
  <link rel="stylesheet" href="css/tab-dark.css" />
  <link rel="stylesheet" href="css/patternfly.css" />
  <link rel="stylesheet" href="css/patternfly-addons.css" />

  <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
  <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
  <script src="https://kit.fontawesome.com/8a8c57f9cf.js" crossorigin="anonymous"></script>

  <?php
  // Load profile definitions
  $profiles = require_once __DIR__ . '/ds-qualifier/profiles.php';
  // Load questions to get domain names
  $questions = require_once __DIR__ . '/ds-qualifier/config.php';
  $domainNames = array_keys($questions);
  ?>

  <style>
    body {
      background-color: #151515 !important;
      color: #ccc !important;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
      margin: 0;
    }

    .landing-page-wrapper {
      flex: 1 0 auto;
      min-height: calc(100vh - 200px);
      display: flex;
      flex-direction: column;
    }

    .landing-cards-grid {
      display: flex;
      justify-content: center;
      gap: 2rem;
      margin: 2rem 0;
    }

    .landing-cards-grid .landing-card {
      max-width: 1200px;
      width: 100%;
    }

    .landing-card {
      background: #2a2a2a;
      border: 1px solid #444;
      border-radius: 8px;
      padding: 1.75rem;
      transition: all 0.3s ease;
    }

    .landing-card:hover {
      border-color: #0d60f8;
      box-shadow: 0 4px 16px rgba(13, 96, 248, 0.3);
      transform: translateY(-4px);
    }

    .landing-card-header {
      text-align: center;
      margin-bottom: 1rem;
      padding-bottom: 1rem;
      border-bottom: 2px solid #444;
    }

    .landing-card-header i {
      font-size: 3rem;
      color: #12bbd4;
      margin-bottom: 0.5rem;
      display: block;
    }

    .landing-card-header h2 {
      color: #9ec7fc;
      font-size: 1.5rem;
      margin: 0;
    }

    .landing-card-description {
      color: #ccc;
      line-height: 1.6;
      margin-bottom: 1rem;
      text-align: center;
      font-size: 0.95rem;
    }

    .landing-card-content {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 2rem;
      margin-top: 1.5rem;
    }

    .landing-card-left {
      display: flex;
      flex-direction: column;
      gap: 1rem;
    }

    .landing-card-right {
      display: flex;
      flex-direction: column;
    }

    .landing-card-buttons {
      display: flex;
      flex-direction: column;
      gap: 0.75rem;
      margin-top: auto;
    }

    .landing-button {
      display: inline-block;
      padding: 1rem 1.5rem;
      border-radius: 4px;
      text-decoration: none;
      font-weight: 600;
      text-align: center;
      transition: all 0.2s ease;
      border: none;
      cursor: pointer;
      font-size: 1rem;
    }

    .landing-button i {
      margin-right: 0.5rem;
    }

    .landing-button-primary {
      background: linear-gradient(135deg, #0d60f8 0%, #004cbf 100%);
      color: #fff;
    }

    .landing-button-primary:hover {
      background: linear-gradient(135deg, #4d90fe 0%, #0d60f8 100%);
      box-shadow: 0 4px 12px rgba(13, 96, 248, 0.4);
    }

    /* Profile Selector Styles */
    .profile-selector {
      margin-bottom: 1.5rem;
    }

    .profile-selector label {
      display: block;
      color: #9ec7fc;
      font-weight: 600;
      margin-bottom: 0.5rem;
      font-size: 1rem;
    }

    .profile-selector select {
      width: 100%;
      padding: 0.75rem;
      font-size: 1rem;
      background: #1a1a1a;
      border: 1px solid #444;
      border-radius: 4px;
      color: #ccc;
      cursor: pointer;
      transition: all 0.2s ease;
    }

    .profile-selector select:hover {
      border-color: #0d60f8;
    }

    .profile-selector select:focus {
      outline: none;
      border-color: #0d60f8;
      box-shadow: 0 0 0 2px rgba(13, 96, 248, 0.2);
    }

    .profile-description {
      margin-top: 0.75rem;
      padding: 0.75rem;
      background: #1a1a1a;
      border-left: 3px solid #0d60f8;
      border-radius: 4px;
      font-size: 0.85rem;
      color: #999;
      line-height: 1.4;
    }

    .profile-description i {
      margin-right: 0.5rem;
      color: #0d60f8;
    }

    /* Domain Weights Display */
    .weights-display {
      padding: 1rem;
      background: #1a1a1a;
      border: 1px solid #444;
      border-radius: 4px;
      height: 100%;
    }

    .weights-display h3 {
      color: #9ec7fc;
      font-size: 1rem;
      margin: 0 0 0.75rem 0;
      text-align: center;
      font-weight: 600;
    }

    .weight-item {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0.4rem 0;
      border-bottom: 1px solid #333;
    }

    .weight-item:last-child {
      border-bottom: none;
    }

    .weight-domain {
      color: #ccc;
      font-size: 0.85rem;
      flex: 1;
      min-width: 140px;
    }

    .weight-bar-container {
      flex: 1;
      margin: 0 0.75rem;
      height: 8px;
      background: #2a2a2a;
      border-radius: 4px;
      overflow: hidden;
    }

    .weight-bar {
      height: 100%;
      background: linear-gradient(90deg, #0d60f8 0%, #12bbd4 100%);
      transition: width 0.3s ease;
      border-radius: 4px;
    }

    .weight-bar.critical {
      background: linear-gradient(90deg, #f0ab00 0%, #c58c00 100%);
    }

    .weight-value {
      min-width: 40px;
      text-align: right;
      color: #9ec7fc;
      font-weight: 600;
      font-size: 0.9rem;
    }

    /* Custom Weights Sliders */
    .custom-weights-section {
      margin-top: 1rem;
      padding: 1rem;
      background: #1a1a1a;
      border: 1px solid #0d60f8;
      border-radius: 4px;
      display: none;
    }

    .custom-weights-section.active {
      display: block;
    }

    .custom-weights-section h4 {
      color: #9ec7fc;
      font-size: 0.9rem;
      margin: 0 0 1rem 0;
      text-align: center;
    }

    .custom-weight-control {
      display: flex;
      align-items: center;
      gap: 1rem;
      margin-bottom: 0.75rem;
    }

    .custom-weight-control label {
      flex: 1;
      color: #ccc;
      font-size: 0.85rem;
      font-weight: normal;
    }

    .custom-weight-control input[type="range"] {
      flex: 2;
      height: 6px;
      background: #2a2a2a;
      border-radius: 3px;
      outline: none;
      -webkit-appearance: none;
    }

    .custom-weight-control input[type="range"]::-webkit-slider-thumb {
      -webkit-appearance: none;
      appearance: none;
      width: 16px;
      height: 16px;
      background: #0d60f8;
      border-radius: 50%;
      cursor: pointer;
    }

    .custom-weight-control input[type="range"]::-moz-range-thumb {
      width: 16px;
      height: 16px;
      background: #0d60f8;
      border-radius: 50%;
      cursor: pointer;
      border: none;
    }

    .custom-weight-control .weight-slider-value {
      min-width: 50px;
      text-align: right;
      color: #9ec7fc;
      font-weight: 600;
      font-size: 0.9rem;
    }

    @media (max-width: 1024px) {
      .landing-card-content {
        grid-template-columns: 1fr;
        gap: 1.5rem;
      }
    }

    @media (max-width: 768px) {
      .landing-cards-grid {
        grid-template-columns: 1fr;
      }

      .weight-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
      }

      .weight-bar-container {
        width: 100%;
        margin: 0;
      }
    }

    /* Disclaimer Footer Styles */
    .disclaimer-footer {
      flex-shrink: 0;
      background-color: #1a1a1a;
      border-top: 1px solid #444;
      padding: 1.5rem 2rem;
      text-align: center;
      margin-top: auto;
    }

    .disclaimer-footer p {
      color: #999;
      margin: 0;
      font-size: 0.9rem;
    }

    .disclaimer-footer strong {
      color: #ccc;
    }
  </style>
</head>

<body>
  <header class="pf-c-page__header">
    <div class="pf-c-page__header-brand">
      <div class="pf-c-page__header-brand-toggle"></div>
      <a class="pf-c-page__header-brand-link" href="index.php">
        <img class="pf-c-brand" src="images/viewfinder-logo.png" alt="Viewfinder logo" />
      </a>
    </div>
  </header>

  <div class="landing-page-wrapper">
    <div class="container" style="max-width: 1400px; margin: 2rem auto; padding: 0 2rem;">
      <div style="text-align: center; margin-bottom: 1.5rem;">
        <h1 style="color: #9ec7fc; font-size: 2rem; margin-bottom: 0.5rem; font-weight: 600;">
          Digital Sovereignty Navigator
        </h1>
        <p style="color: #ccc; font-size: 1rem; max-width: 800px; margin: 0 auto;">
          Discover what you know and where you stand to help unlock strategies which strengthen your digital independence
        </p>
      </div>

      <div class="landing-cards-grid">
        <!-- Digital Sovereignty Readiness Assessment Card -->
        <div class="landing-card">
          <div class="landing-card-header">
            <i class="fa-solid fa-clipboard-check"></i>
            <h2>Digital Sovereignty Readiness Assessment</h2>
          </div>
          <p class="landing-card-description">
            Quick 10-15 minute assessment to evaluate your organization's digital sovereignty readiness across 7 key domains
          </p>

          <div class="landing-card-content">
            <!-- Left Column: Profile Selector -->
            <div class="landing-card-left">
              <div class="profile-selector">
                <label for="profile-select">
                  <i class="fa-solid fa-layer-group"></i> Select Your Industry/Context:
                </label>
                <select id="profile-select" name="profile">
                  <?php foreach ($profiles as $profileKey => $profileData): ?>
                    <option value="<?php echo htmlspecialchars($profileKey); ?>"
                            data-description="<?php echo htmlspecialchars($profileData['description']); ?>"
                            data-icon="<?php echo htmlspecialchars($profileData['icon']); ?>"
                            <?php echo $profileKey === 'balanced' ? 'selected' : ''; ?>>
                      <?php echo htmlspecialchars($profileData['name']); ?>
                    </option>
                  <?php endforeach; ?>
                </select>
                <div class="profile-description" id="profile-description">
                  <i class="fa-solid fa-balance-scale"></i>
                  <span id="profile-description-text">
                    <?php echo htmlspecialchars($profiles['balanced']['description']); ?>
                  </span>
                </div>
              </div>

              <!-- Custom Weights Controls (shown only when Custom profile is selected) -->
              <div class="custom-weights-section" id="custom-weights-section">
                <h4><i class="fa-solid fa-sliders"></i> Customize Domain Weights</h4>
                <p style="font-size: 0.8rem; color: #999; margin-bottom: 1rem; text-align: center;">
                  Adjust weights from 1.0× (standard) to 2.0× (critical priority)
                </p>
                <?php foreach ($domainNames as $domain): ?>
                  <div class="custom-weight-control">
                    <label for="slider-<?php echo htmlspecialchars(str_replace(' ', '-', $domain)); ?>">
                      <?php echo htmlspecialchars($domain); ?>
                    </label>
                    <input
                      type="range"
                      id="slider-<?php echo htmlspecialchars(str_replace(' ', '-', $domain)); ?>"
                      name="weight-<?php echo htmlspecialchars(str_replace(' ', '-', $domain)); ?>"
                      min="1.0"
                      max="2.0"
                      step="0.5"
                      value="1.0"
                      data-domain="<?php echo htmlspecialchars($domain); ?>"
                    >
                    <span class="weight-slider-value" id="slider-value-<?php echo htmlspecialchars(str_replace(' ', '-', $domain)); ?>">1.0×</span>
                  </div>
                <?php endforeach; ?>
              </div>

              <div class="landing-card-buttons">
                <button id="start-assessment-btn" class="landing-button landing-button-primary">
                  <i class="fa-solid fa-rocket"></i> Start Assessment
                </button>
              </div>
            </div>

            <!-- Right Column: Domain Weights Display -->
            <div class="landing-card-right">
              <div class="weights-display">
                <h3>
                  <i class="fa-solid fa-chart-bar"></i> Domain Weighting - <span id="profile-name-display">Balanced</span>
                </h3>
                <div id="weights-container">
                  <?php foreach ($domainNames as $domain): ?>
                    <div class="weight-item">
                      <span class="weight-domain"><?php echo htmlspecialchars($domain); ?></span>
                      <div class="weight-bar-container">
                        <div class="weight-bar" id="weight-bar-<?php echo htmlspecialchars(str_replace(' ', '-', $domain)); ?>" style="width: 50%;"></div>
                      </div>
                      <span class="weight-value" id="weight-value-<?php echo htmlspecialchars(str_replace(' ', '-', $domain)); ?>">1.0×</span>
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <script>
        // Profile data embedded from PHP
        const profilesData = <?php echo json_encode($profiles); ?>;
        const domainNames = <?php echo json_encode($domainNames); ?>;

        // Custom weights storage
        let customWeights = {};

        // Initialize custom weights with defaults
        domainNames.forEach(domain => {
          customWeights[domain] = 1.0;
        });

        // Update weights display when profile changes
        function updateWeightsDisplay(profileKey, useCustomWeights = false) {
          const profile = profilesData[profileKey];
          const weights = useCustomWeights ? customWeights : profile.weights;

          // Update profile description
          const descIcon = document.querySelector('.profile-description i');
          descIcon.className = 'fa-solid ' + profile.icon;
          document.getElementById('profile-description-text').textContent = profile.description;

          // Update profile name in Domain Weighting header
          document.getElementById('profile-name-display').textContent = profile.name;

          // Show/hide custom weights section
          const customSection = document.getElementById('custom-weights-section');
          if (profileKey === 'custom') {
            customSection.classList.add('active');
          } else {
            customSection.classList.remove('active');
          }

          // Update each domain weight
          Object.keys(weights).forEach(domain => {
            const weight = weights[domain];
            const domainId = domain.replace(/ /g, '-');
            const barElement = document.getElementById('weight-bar-' + domainId);
            const valueElement = document.getElementById('weight-value-' + domainId);

            if (barElement && valueElement) {
              // Calculate percentage (max weight is 2.0 = 100%)
              const percentage = (weight / 2.0) * 100;
              barElement.style.width = percentage + '%';

              // Add critical class for weights >= 1.5
              if (weight >= 1.5) {
                barElement.classList.add('critical');
              } else {
                barElement.classList.remove('critical');
              }

              valueElement.textContent = weight.toFixed(1) + '×';
            }

            // Update slider if in custom mode
            if (profileKey === 'custom') {
              const slider = document.getElementById('slider-' + domainId);
              if (slider) {
                slider.value = weight;
                const sliderValue = document.getElementById('slider-value-' + domainId);
                if (sliderValue) {
                  sliderValue.textContent = weight.toFixed(1) + '×';
                }
              }
            }
          });
        }

        // Handle slider changes
        domainNames.forEach(domain => {
          const domainId = domain.replace(/ /g, '-');
          const slider = document.getElementById('slider-' + domainId);

          if (slider) {
            slider.addEventListener('input', function() {
              const weight = parseFloat(this.value);
              customWeights[domain] = weight;

              // Update slider value display
              const sliderValue = document.getElementById('slider-value-' + domainId);
              if (sliderValue) {
                sliderValue.textContent = weight.toFixed(1) + '×';
              }

              // Update weight visualization
              updateWeightsDisplay('custom', true);
            });
          }
        });

        // Initialize with default profile
        updateWeightsDisplay('balanced');

        // Listen for profile selection changes
        document.getElementById('profile-select').addEventListener('change', function() {
          updateWeightsDisplay(this.value);
        });

        // Handle start assessment button
        document.getElementById('start-assessment-btn').addEventListener('click', function() {
          const selectedProfile = document.getElementById('profile-select').value;

          // Build URL with profile
          let url = 'ds-qualifier/?profile=' + encodeURIComponent(selectedProfile);

          // If custom profile, add custom weights as URL parameters
          if (selectedProfile === 'custom') {
            domainNames.forEach(domain => {
              const weight = customWeights[domain];
              url += '&weight_' + encodeURIComponent(domain.replace(/ /g, '_')) + '=' + weight;
            });
          }

          window.location.href = url;
        });
      </script>
    </div>
  </div>

  <footer class="disclaimer-footer">
    <p><strong>Disclaimer:</strong> This Cloud Sovereignty Framework Self-Assessment Tool is provided to help organizations review their sovereign posture. It is not endorsed by any regulatory authority, and its findings or recommendations do not constitute legal advice. We bear no legal responsibility or liability for the results or its use.</p>
  </footer>
</body>
</html>
