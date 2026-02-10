<!doctype html>
<html lang="en-us" class="pf-theme-dark">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Results - Digital Sovereignty Readiness Assessment</title>

  <!-- Reuse existing CSS from parent directory -->
  <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
  <link rel="stylesheet" href="../css/bootstrap.min.css">
  <link rel="stylesheet" href="../css/brands.css" />
  <link rel="stylesheet" href="../css/style.css" />
  <link rel="stylesheet" href="../css/tab-dark.css" />
  <link rel="stylesheet" href="../css/patternfly.css" />
  <link rel="stylesheet" href="../css/patternfly-addons.css" />

  <!-- DS Qualifier specific styles -->
  <link rel="stylesheet" href="css/ds-qualifier.css" />

  <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
  <script src="https://kit.fontawesome.com/8a8c57f9cf.js" crossorigin="anonymous"></script>

  <style>
    body {
      background-color: #151515 !important;
      color: #ccc !important;
    }
    .pf-c-page__header-tools button {
      margin-right: 1rem;
    }
    @media print {
      .no-print { display: none; }
      .score-card { page-break-after: avoid; }
    }
  </style>
</head>

<body>
  <header class="pf-c-page__header no-print">
    <div class="pf-c-page__header-brand">
      <div class="pf-c-page__header-brand-toggle"></div>
      <a class="pf-c-page__header-brand-link" href="../index.php">
        <img class="pf-c-brand" src="../images/viewfinder-logo.png" alt="Viewfinder logo" />
      </a>
    </div>

    <div class="widget">
      <a href="../index.php"><button><i class="fa-solid fa-home"></i> Home</button></a>
      <a href="index.php"><button style="margin-left: 1rem;">New Assessment</button></a>
    </div>
  </header>

  <div class="container">
    <?php
    // Start session to store results for PDF generation
    session_start();

    // Store POST data in session for PDF generator
    $_SESSION['assessment_data'] = $_POST;

    // Load questions configuration for domain mapping
    $questions = require_once 'config.php';

    // Load profiles and get selected profile
    $profiles = require_once 'profiles.php';
    $selectedProfile = isset($_POST['profile']) ? $_POST['profile'] : 'balanced';

    // Validate profile exists
    if (!isset($profiles[$selectedProfile])) {
        $selectedProfile = 'balanced';
    }

    $profileData = $profiles[$selectedProfile];

    // Handle custom weights if custom profile is selected
    if ($selectedProfile === 'custom') {
        $domainWeights = [];
        foreach ($questions as $domainName => $domainData) {
            $paramName = 'custom_weight_' . str_replace(' ', '_', $domainName);
            if (isset($_POST[$paramName])) {
                $weight = floatval($_POST[$paramName]);
                // Validate weight is between 1.0 and 2.0
                $domainWeights[$domainName] = max(1.0, min(2.0, $weight));
            } else {
                $domainWeights[$domainName] = 1.0;
            }
        }
        // Update profile data description for custom
        if (array_sum($domainWeights) == count($domainWeights)) {
            $profileData['description'] = 'Custom profile with balanced weighting (all domains set to 1.0×)';
        } else {
            $profileData['description'] = 'Custom profile with user-defined domain weightings';
        }
    } else {
        $domainWeights = $profileData['weights'];
    }

    // Initialize scoring arrays
    $totalScore = 0;
    $weightedScore = 0;
    $maxScore = 21;
    $domainScores = [];
    $domainMaxScores = [];
    $domainWeightedScores = [];
    $domainResponses = [];
    $unknownQuestions = []; // Track "Don't Know" responses

    // Map domain keys to display names
    $domainKeyMap = [];
    foreach ($questions as $domainName => $domainData) {
        $domainKeyMap[$domainData['domain_key']] = $domainName;
        $domainScores[$domainName] = 0;
        $domainMaxScores[$domainName] = count($domainData['questions']);
        $domainResponses[$domainName] = [];
    }

    // Calculate scores (both raw and weighted)
    foreach ($_POST as $key => $value) {
        // Match question IDs (ds1, ts1, os1, etc.)
        if (preg_match('/^(ds|ts|os|as|oss|eo|ms)\d+$/', $key)) {
            // Find which domain this question belongs to
            foreach ($questions as $domainName => $domainData) {
                foreach ($domainData['questions'] as $question) {
                    if ($question['id'] === $key) {
                        // Handle "Don't Know" responses
                        if ($value === 'unknown') {
                            $unknownQuestions[] = [
                                'domain' => $domainName,
                                'question' => $question['text'],
                                'tooltip' => $question['tooltip'] ?? ''
                            ];
                            // Don't count toward score, but don't penalize either
                        } else {
                            $intValue = intval($value);
                            $totalScore += $intValue;
                            $domainScores[$domainName] += $intValue;
                            // Only add to responses if answer was "Yes" (value > 0)
                            if ($intValue > 0) {
                                $domainResponses[$domainName][] = $question['text'];
                            }
                        }
                        break 2;
                    }
                }
            }
        }
    }

    // Calculate weighted scores per domain
    $totalWeight = 0;
    $weightedSum = 0;

    foreach ($domainScores as $domainName => $score) {
        $maxForDomain = $domainMaxScores[$domainName];
        $weight = $domainWeights[$domainName] ?? 1.0;

        // Calculate percentage for this domain (0-100%)
        $domainPercentage = $maxForDomain > 0 ? ($score / $maxForDomain) : 0;

        // Apply weight
        $weightedDomainScore = $domainPercentage * $weight;
        $domainWeightedScores[$domainName] = $weightedDomainScore;

        $weightedSum += $weightedDomainScore;
        $totalWeight += $weight;
    }

    // Normalize weighted score to 0-21 scale
    $weightedScore = $totalWeight > 0 ? ($weightedSum / $totalWeight) * 21 : 0;

    // Determine maturity level based on WEIGHTED score (4-level system)
    // Foundation: 0-25% (0-5.25 points), Developing: 26-50% (5.26-10.5 points)
    // Strategic: 51-75% (10.51-15.75 points), Advanced: 76-100% (15.76-21 points)
    if ($weightedScore <= 5.25) {
        $maturityLevel = 'Foundation';
        $priorityClass = 'maturity-foundation';
        $priorityIcon = 'fa-seedling';
        $recommendation = 'Foundation Level';
        $recommendationDetail = 'Your organization is in the early stages of digital sovereignty. Significant opportunities exist to strengthen capabilities across multiple domains and reduce dependencies on external providers.';
    } elseif ($weightedScore <= 10.5) {
        $maturityLevel = 'Developing';
        $priorityClass = 'maturity-developing';
        $priorityIcon = 'fa-arrow-trend-up';
        $recommendation = 'Developing Level';
        $recommendationDetail = 'Your organization is actively building digital sovereignty capabilities and making progress. Continue developing your foundational controls and addressing gaps to move toward strategic maturity.';
    } elseif ($weightedScore <= 15.75) {
        $maturityLevel = 'Strategic';
        $priorityClass = 'maturity-strategic';
        $priorityIcon = 'fa-chart-line';
        $recommendation = 'Strategic Level';
        $recommendationDetail = 'Your organization has established strong digital sovereignty capabilities across most domains. Focus on closing remaining gaps and optimizing existing controls to achieve advanced maturity.';
    } else {
        $maturityLevel = 'Advanced';
        $priorityClass = 'maturity-advanced';
        $priorityIcon = 'fa-shield-halved';
        $recommendation = 'Advanced Level';
        $recommendationDetail = 'Your organization demonstrates comprehensive digital sovereignty capabilities across all domains. Continue maintaining excellence and stay ahead of evolving regulatory and geopolitical requirements.';
    }

    $assessmentDate = date('F j, Y \a\t g:i A');
    ?>

    <!-- Results Header -->
    <div class="results-header">
      <h1><i class="fa-solid fa-chart-bar"></i> Digital Sovereignty Readiness Assessment Results</h1>
      <p class="assessment-date"><strong>Assessment Date:</strong> <?php echo $assessmentDate; ?></p>

      <!-- Profile Information -->
      <div style="text-align: center; margin-top: 1rem; padding: 1rem; background: #1a1a1a; border-radius: 4px; border: 1px solid #444;">
        <i class="fa-solid <?php echo htmlspecialchars($profileData['icon']); ?>" style="color: #0d60f8; margin-right: 0.5rem; font-size: 1.2rem;"></i>
        <strong style="color: #9ec7fc; font-size: 1.1rem;">Profile:</strong>
        <span style="color: #fff; font-size: 1.1rem; margin-left: 0.5rem;"><?php echo htmlspecialchars($profileData['name']); ?></span>
        <p style="color: #999; margin: 0.5rem 0 0 0; font-size: 0.9rem;">
          <?php echo htmlspecialchars($profileData['description']); ?>
        </p>
      </div>
    </div>

    <!-- Score Card -->
    <div class="score-card <?php echo $priorityClass; ?>">
      <div class="score-icon">
        <i class="fa-solid <?php echo $priorityIcon; ?>"></i>
      </div>
      <h2><?php echo $maturityLevel; ?> Maturity Level</h2>

      <?php
      // Calculate percentage for visual display (based on weighted score)
      $scorePercentage = round(($weightedScore / $maxScore) * 100);
      ?>

      <div class="score-visual-container">
        <div class="circular-progress" data-percentage="<?php echo $scorePercentage; ?>">
          <svg class="progress-ring" width="200" height="200">
            <circle class="progress-ring-circle-bg" cx="100" cy="100" r="90" />
            <circle class="progress-ring-circle"
                    cx="100"
                    cy="100"
                    r="90"
                    style="stroke-dasharray: <?php echo 2 * 3.14159 * 90; ?>; stroke-dashoffset: <?php echo 2 * 3.14159 * 90 * (1 - $scorePercentage / 100); ?>;" />
          </svg>
          <div class="progress-text">
            <div class="percentage-display"><?php echo $scorePercentage; ?>%</div>
            <div class="score-detail">
              <strong><?php echo number_format($weightedScore, 1); ?></strong> of <?php echo $maxScore; ?> points
              <br>
              <span style="font-size: 0.8rem; color: #999;">(Raw: <?php echo $totalScore; ?> pts)</span>
            </div>
          </div>
        </div>
      </div>

      <h3 class="recommendation-title"><?php echo $recommendation; ?></h3>
      <p class="recommendation-detail"><?php echo $recommendationDetail; ?></p>
    </div>

    <!-- Domain Breakdown -->
    <div class="domain-breakdown">
      <h2><i class="fa-solid fa-table"></i> Domain Analysis</h2>
      <p class="section-intro">Breakdown of your readiness across the 7 Digital Sovereignty domains:</p>
      <p class="section-intro" style="font-size: 0.9rem; color: #999; font-style: italic;">
        <i class="fa-solid fa-info-circle"></i> Weights reflect the importance of each domain for the <strong><?php echo htmlspecialchars($profileData['name']); ?></strong> profile.
        Domains with higher weights (≥1.5×) contribute more to your overall score.
      </p>

      <div class="domain-table-wrapper">
        <table class="domain-table">
          <thead>
            <tr>
              <th>Domain</th>
              <th style="text-align: center;">Score</th>
              <th style="text-align: center;">Weight</th>
              <th style="text-align: center;">Progress</th>
              <th>Maturity Level</th>
            </tr>
          </thead>
          <tbody>
            <?php
            foreach ($questions as $domainName => $domainData):
                $score = $domainScores[$domainName] ?? 0;
                $maxDomainScore = count($domainData['questions']);
                $percentage = ($score / $maxDomainScore) * 100;
                $weight = $domainWeights[$domainName] ?? 1.0;

                // Maturity levels based on score percentage (4-level system)
                // Foundation: 0%, Developing: 1-33%, Strategic: 34-67%, Advanced: 68-100%
                if ($percentage == 0) {
                    $strengthClass = 'strength-foundation';
                    $strengthIcon = 'fa-seedling';
                    $strengthText = 'Foundation';
                } elseif ($percentage <= 33) {
                    $strengthClass = 'strength-developing';
                    $strengthIcon = 'fa-arrow-trend-up';
                    $strengthText = 'Developing';
                } elseif ($percentage <= 67) {
                    $strengthClass = 'strength-strategic';
                    $strengthIcon = 'fa-chart-line';
                    $strengthText = 'Strategic';
                } else {
                    $strengthClass = 'strength-advanced';
                    $strengthIcon = 'fa-shield-halved';
                    $strengthText = 'Advanced';
                }
            ?>
              <tr>
                <td><strong><?php echo htmlspecialchars($domainName); ?></strong></td>
                <td style="text-align: center;">
                  <span class="domain-score-cell"><?php echo $score; ?>/<?php echo $maxDomainScore; ?></span>
                </td>
                <td style="text-align: center;">
                  <span class="weight-badge" style="display: inline-block; padding: 0.25rem 0.75rem; background: <?php echo $weight >= 1.5 ? 'linear-gradient(135deg, #f0ab00 0%, #c58c00 100%)' : '#1a1a1a'; ?>; border: 1px solid #444; border-radius: 4px; font-weight: 600; color: <?php echo $weight >= 1.5 ? '#fff' : '#9ec7fc'; ?>;">
                    <?php echo number_format($weight, 1); ?>×
                  </span>
                </td>
                <td style="text-align: center;">
                  <span class="progress-bar-wrapper">
                    <div class="progress-bar">
                      <div class="progress-fill <?php echo $strengthClass; ?>" style="width: <?php echo $percentage; ?>%;"></div>
                    </div>
                  </span>
                </td>
                <td>
                  <span class="strength-badge <?php echo $strengthClass; ?>">
                    <i class="fa-solid <?php echo $strengthIcon; ?>"></i> <?php echo $strengthText; ?>
                  </span>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Questions to Research -->
    <?php if (!empty($unknownQuestions)): ?>
    <div class="unknown-questions-section">
      <h2><i class="fa-solid fa-clipboard-question"></i> Questions to Research</h2>
      <p class="section-description">
        The following questions were marked as "Don't Know". Research these areas to get a complete picture
        of your organization's Digital Sovereignty readiness and identify opportunities for improvement.
      </p>

      <?php
      // Group unknown questions by domain
      $unknownByDomain = [];
      foreach ($unknownQuestions as $uq) {
        $unknownByDomain[$uq['domain']][] = $uq;
      }
      ?>

      <div class="unknown-questions-list">
        <?php foreach ($unknownByDomain as $domainName => $domainUnknowns): ?>
          <div class="unknown-domain-section">
            <h3><i class="fa-solid fa-folder-open"></i> <?php echo htmlspecialchars($domainName); ?></h3>
            <ul class="unknown-question-items">
              <?php foreach ($domainUnknowns as $uq): ?>
                <li class="unknown-question-item">
                  <span class="question-icon"><i class="fa-solid fa-question-circle"></i></span>
                  <div class="question-content">
                    <div class="question-text"><?php echo htmlspecialchars($uq['question']); ?></div>
                    <?php if (!empty($uq['tooltip'])): ?>
                      <div class="question-context">
                        <i class="fa-solid fa-lightbulb"></i>
                        <strong>Context:</strong> <?php echo htmlspecialchars($uq['tooltip']); ?>
                      </div>
                    <?php endif; ?>
                  </div>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endforeach; ?>
      </div>

      <div class="discovery-tip">
        <i class="fa-solid fa-circle-info"></i>
        <strong>Tip:</strong> Understanding these areas will help you identify gaps in your digital sovereignty posture
        and prioritize improvements to strengthen your organization's independence and resilience.
      </div>
    </div>
    <?php endif; ?>

    <!-- Improvement Actions -->
    <div class="improvement-actions">
      <h2><i class="fa-solid fa-bullseye"></i> Recommended Improvement Actions</h2>

      <?php if ($maturityLevel === 'Foundation'): ?>
        <div class="action-priority maturity-foundation">
          <h3><i class="fa-solid fa-seedling"></i> Priority Actions for Foundation Level</h3>
          <p>Your organization is in the early stages of digital sovereignty. Focus on building foundational capabilities:</p>
          <ul>
            <li><strong>Assess Current State:</strong> Conduct detailed inventory of data locations, vendor dependencies, and compliance requirements</li>
            <li><strong>Define Strategy:</strong> Develop a digital sovereignty roadmap aligned with your business objectives and regulatory obligations</li>
            <li><strong>Establish Governance:</strong> Create executive sponsorship and steering committee for sovereignty initiatives</li>
            <li><strong>Address Quick Wins:</strong> Implement encryption key management (BYOK/HYOK) and data residency controls</li>
            <li><strong>Build Expertise:</strong> Train technical teams on sovereign technologies and compliance frameworks</li>
            <li><strong>Evaluate Solutions:</strong> Research open-source and sovereign-ready platforms that reduce vendor lock-in</li>
          </ul>

          <div class="recommended-products">
            <h4>Recommended Focus Areas:</h4>
            <ul>
              <li>Data sovereignty and encryption controls</li>
              <li>Open-source adoption strategy</li>
              <li>Compliance framework alignment (GDPR, NIS2, etc.)</li>
              <li>Vendor risk assessment and diversification</li>
            </ul>
          </div>
        </div>

      <?php elseif ($maturityLevel === 'Developing'): ?>
        <div class="action-priority maturity-developing">
          <h3><i class="fa-solid fa-arrow-trend-up"></i> Advancement Actions for Developing Level</h3>
          <p>Your organization is making progress building digital sovereignty capabilities. Continue your momentum:</p>
          <ul>
            <li><strong>Strengthen Foundations:</strong> Solidify controls in domains where you scored lowest (0-1 points)</li>
            <li><strong>Implement Standards:</strong> Adopt open standards and containerization to improve portability</li>
            <li><strong>Enhance Data Controls:</strong> Ensure all sensitive data has proper residency and encryption controls</li>
            <li><strong>Build Resilience:</strong> Develop disaster recovery and business continuity plans for geopolitical scenarios</li>
            <li><strong>Expand Expertise:</strong> Grow in-house technical capabilities for managing sovereign infrastructure</li>
            <li><strong>Document Policies:</strong> Create formal policies for open-source adoption and vendor selection</li>
          </ul>

          <div class="recommended-products">
            <h4>Recommended Focus Areas:</h4>
            <ul>
              <li>Cloud platform portability and migration testing</li>
              <li>Security log sovereignty and audit controls</li>
              <li>Operational independence from external providers</li>
              <li>Executive alignment and budget allocation</li>
            </ul>
          </div>
        </div>

      <?php elseif ($maturityLevel === 'Strategic'): ?>
        <div class="action-priority maturity-strategic">
          <h3><i class="fa-solid fa-chart-line"></i> Growth Actions for Strategic Level</h3>
          <p>Your organization has established some capabilities. Continue building momentum:</p>
          <ul>
            <li><strong>Close Remaining Gaps:</strong> Address specific weaknesses identified in lower-scoring domains</li>
            <li><strong>Enhance Portability:</strong> Migrate workloads to open standards and test cloud portability</li>
            <li><strong>Strengthen Controls:</strong> Implement advanced monitoring, audit rights, and security log sovereignty</li>
            <li><strong>Expand Open Source:</strong> Increase use of open-source software and participate in strategic projects</li>
            <li><strong>Test Resilience:</strong> Validate disaster recovery plans and operational independence from cloud providers</li>
            <li><strong>Pursue Certifications:</strong> Obtain national security certifications (NIS2, SecNumCloud, etc.)</li>
          </ul>

          <div class="recommended-resources">
            <h4>Recommended Resources:</h4>
            <ul>
              <li>Digital Sovereignty best practices and frameworks</li>
              <li>Cloud migration and portability guides</li>
              <li>National certification requirements documentation</li>
              <li>Open-source governance policies</li>
            </ul>
          </div>
        </div>

      <?php else: ?>
        <div class="action-priority maturity-advanced">
          <h3><i class="fa-solid fa-shield-halved"></i> Optimization Actions for Advanced Level</h3>
          <p>Your organization demonstrates strong sovereignty capabilities. Maintain and enhance your position:</p>
          <ul>
            <li><strong>Maintain Excellence:</strong> Continuously monitor and update sovereignty controls as regulations evolve</li>
            <li><strong>Share Knowledge:</strong> Document and share best practices internally and with industry peers</li>
            <li><strong>Lead Innovation:</strong> Contribute to open-source projects and influence sovereignty standards</li>
            <li><strong>Expand Scope:</strong> Apply sovereignty principles to emerging technologies (AI, edge computing, IoT)</li>
            <li><strong>Regular Validation:</strong> Conduct periodic audits and re-certifications to maintain compliance</li>
            <li><strong>Stay Informed:</strong> Monitor geopolitical changes and emerging regulations that may impact your strategy</li>
          </ul>

          <p class="note"><strong>Note:</strong> Digital sovereignty is a continuous journey. Regulations and threats evolve, requiring ongoing attention and investment to maintain your advanced posture.</p>
        </div>
      <?php endif; ?>
    </div>

    <!-- Detailed Domain Insights -->
    <div class="domain-insights">
      <h2><i class="fa-solid fa-list-check"></i> Detailed Domain Insights</h2>
      <p class="section-intro">Review your specific responses across all domains:</p>

      <?php foreach ($questions as $domainName => $domainData):
          $score = $domainScores[$domainName] ?? 0;
          $responses = $domainResponses[$domainName] ?? [];

          if ($score > 0):
      ?>
        <div class="domain-insight-card">
          <div class="domain-insight-header">
            <h3><?php echo htmlspecialchars($domainName); ?></h3>
            <span class="insight-score"><?php echo $score; ?>/<?php echo count($domainData['questions']); ?></span>
          </div>
          <p class="domain-insight-description"><?php echo htmlspecialchars($domainData['description']); ?></p>

          <div class="requirements-found">
            <h4>Requirements Identified:</h4>
            <ul>
              <?php foreach ($responses as $response): ?>
                <li><i class="fa-solid fa-check"></i> <?php echo htmlspecialchars($response); ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        </div>
      <?php
          endif;
        endforeach;
      ?>

      <?php if ($totalScore === 0): ?>
        <div class="no-requirements">
          <p><i class="fa-solid fa-info-circle"></i> No Digital Sovereignty requirements were identified in this assessment. Consider focusing on other Red Hat value propositions.</p>
        </div>
      <?php endif; ?>
    </div>

    <!-- Action Buttons -->
    <div class="form-actions no-print">
      <a href="generate-pdf.php" class="btn-primary">
        <i class="fa-solid fa-file-pdf"></i> Download PDF
      </a>
      <a href="index.php" class="btn-secondary">
        <i class="fa-solid fa-rotate-left"></i> New Assessment
      </a>
    </div>

    <!-- Footer -->
    <div class="results-footer">
      <p><small>Generated by Viewfinder Digital Sovereignty Readiness Assessment on <?php echo $assessmentDate; ?></small></p>
    </div>
  </div>
</body>
</html>
