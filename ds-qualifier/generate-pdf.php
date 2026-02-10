<?php
/**
 * PDF Generation for Digital Sovereignty Readiness Assessment Results
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Start session to retrieve assessment data
session_start();

// Check if we have assessment data in session
if (!isset($_SESSION['assessment_data']) || empty($_SESSION['assessment_data'])) {
    die('No assessment data found. Please complete the assessment first.');
}

// Get assessment data from session
$assessmentData = $_SESSION['assessment_data'];

// Load questions configuration
$questions = require_once 'config.php';

// Load profiles and get selected profile
$profiles = require_once 'profiles.php';
$selectedProfile = isset($assessmentData['profile']) ? $assessmentData['profile'] : 'balanced';

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
        if (isset($assessmentData[$paramName])) {
            $weight = floatval($assessmentData[$paramName]);
            $domainWeights[$domainName] = max(1.0, min(2.0, $weight));
        } else {
            $domainWeights[$domainName] = 1.0;
        }
    }
} else {
    $domainWeights = $profileData['weights'];
}

// Initialize scoring arrays (same logic as results.php)
$totalScore = 0;
$weightedScore = 0;
$maxScore = 21;
$domainScores = [];
$domainMaxScores = [];
$domainWeightedScores = [];
$unknownQuestions = [];

// Initialize domain scores
foreach ($questions as $domainName => $domainData) {
    $domainScores[$domainName] = 0;
    $domainMaxScores[$domainName] = count($domainData['questions']);
}

// Calculate scores - EXACT same logic as results.php
foreach ($assessmentData as $key => $value) {
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
                    } else {
                        $intValue = intval($value);
                        $totalScore += $intValue;
                        $domainScores[$domainName] += $intValue;
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

// Determine maturity level based on WEIGHTED score (same logic as results.php)
if ($weightedScore <= 5.25) {
    $maturityLevel = 'Foundation';
    $maturityColor = '#c9190b';
    $maturityIcon = '🌱';
    $recommendationDetail = 'Your organization is in the early stages of digital sovereignty. Significant opportunities exist to strengthen capabilities across multiple domains and reduce dependencies on external providers.';
} elseif ($weightedScore <= 10.5) {
    $maturityLevel = 'Developing';
    $maturityColor = '#ec7a08';
    $maturityIcon = '📈';
    $recommendationDetail = 'Your organization is actively building digital sovereignty capabilities and making progress. Continue developing your foundational controls and addressing gaps to move toward strategic maturity.';
} elseif ($weightedScore <= 15.75) {
    $maturityLevel = 'Strategic';
    $maturityColor = '#f0ab00';
    $maturityIcon = '📊';
    $recommendationDetail = 'Your organization has established strong digital sovereignty capabilities across most domains. Focus on closing remaining gaps and optimizing existing controls to achieve advanced maturity.';
} else {
    $maturityLevel = 'Advanced';
    $maturityColor = '#2aaa04';
    $maturityIcon = '🛡️';
    $recommendationDetail = 'Your organization demonstrates comprehensive digital sovereignty capabilities across all domains. Continue maintaining excellence and stay ahead of evolving regulatory and geopolitical requirements.';
}

// Calculate percentage based on weighted score
$scorePercentage = round(($weightedScore / $maxScore) * 100);
$assessmentDate = date('F j, Y \a\t g:i A');

// Build HTML for PDF
$html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Digital Sovereignty Readiness Assessment Results</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            font-size: 11pt;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid ' . $maturityColor . ';
            padding-bottom: 20px;
        }
        .header h1 {
            color: #151515;
            margin: 0 0 10px 0;
            font-size: 24px;
        }
        .header .date {
            color: #666;
            font-size: 11px;
        }
        .score-card {
            background: ' . $maturityColor . ';
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 30px;
        }
        .score-card h2 {
            margin: 0 0 15px 0;
            font-size: 26px;
        }
        .score-circle {
            font-size: 42px;
            font-weight: bold;
            margin: 15px 0;
        }
        .score-detail {
            font-size: 13px;
            opacity: 0.9;
        }
        .recommendation {
            margin: 15px 0;
            font-size: 13px;
            line-height: 1.8;
        }
        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        .section h3 {
            color: ' . $maturityColor . ';
            border-bottom: 2px solid ' . $maturityColor . ';
            padding-bottom: 5px;
            margin-bottom: 15px;
            font-size: 16px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        table th {
            background: #f5f5f5;
            padding: 8px;
            text-align: left;
            border: 1px solid #ddd;
            font-weight: bold;
            font-size: 10pt;
        }
        table td {
            padding: 8px;
            border: 1px solid #ddd;
            font-size: 10pt;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            color: white;
            font-weight: bold;
            font-size: 10px;
        }
        .badge-foundation { background: #c9190b; }
        .badge-developing { background: #ec7a08; }
        .badge-strategic { background: #f0ab00; color: #000; }
        .badge-advanced { background: #2aaa04; }
        .unknown-list {
            margin: 15px 0;
        }
        .unknown-item {
            background: #f9f9f9;
            padding: 10px;
            margin: 10px 0;
            border-left: 4px solid #0066cc;
        }
        .unknown-item strong {
            display: block;
            margin-bottom: 5px;
            color: #0066cc;
            font-size: 11pt;
        }
        .improvement-section {
            background: #f9f9f9;
            padding: 15px;
            border-left: 4px solid ' . $maturityColor . ';
            margin: 20px 0;
            page-break-inside: avoid;
        }
        .improvement-section h4 {
            margin-top: 0;
            color: ' . $maturityColor . ';
            font-size: 14px;
        }
        .improvement-section ul {
            margin: 10px 0;
            padding-left: 20px;
        }
        .improvement-section li {
            margin: 8px 0;
            font-size: 10pt;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            font-size: 9pt;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Digital Sovereignty Readiness Assessment Results</h1>
        <div class="date">Assessment Date: ' . htmlspecialchars($assessmentDate) . '</div>
    </div>

    <div class="score-card">
        <h2>' . htmlspecialchars($maturityLevel) . ' Maturity Level</h2>
        <div class="score-circle">' . $scorePercentage . '%</div>
        <div class="score-detail">' . number_format($weightedScore, 1) . ' of ' . $maxScore . ' points (weighted)</div>
        <div class="score-detail" style="font-size: 0.8em; color: #666;">Raw score: ' . $totalScore . ' points | Profile: ' . htmlspecialchars($profileData['name']) . '</div>
        <div class="recommendation">' . htmlspecialchars($recommendationDetail) . '</div>
    </div>

    <div class="section">
        <h3>Domain Analysis</h3>
        <table>
            <thead>
                <tr>
                    <th>Domain</th>
                    <th style="text-align: center;">Score</th>
                    <th style="text-align: center;">Percentage</th>
                    <th>Maturity Level</th>
                </tr>
            </thead>
            <tbody>';

foreach ($questions as $domainName => $domainData) {
    $score = $domainScores[$domainName] ?? 0;
    $maxDomainScore = count($domainData['questions']);
    $percentage = $maxDomainScore > 0 ? round(($score / $maxDomainScore) * 100) : 0;

    if ($percentage == 0) {
        $badge = 'foundation';
        $levelText = 'Foundation';
    } elseif ($percentage <= 33) {
        $badge = 'developing';
        $levelText = 'Developing';
    } elseif ($percentage <= 67) {
        $badge = 'strategic';
        $levelText = 'Strategic';
    } else {
        $badge = 'advanced';
        $levelText = 'Advanced';
    }

    $html .= '<tr>
                <td><strong>' . htmlspecialchars($domainName) . '</strong></td>
                <td style="text-align: center;">' . $score . '/' . $maxDomainScore . '</td>
                <td style="text-align: center;">' . $percentage . '%</td>
                <td><span class="badge badge-' . $badge . '">' . $levelText . '</span></td>
              </tr>';
}

$html .= '  </tbody>
        </table>
    </div>';

// Recommended Improvement Actions section
$html .= '<div class="section">
    <h3>Recommended Improvement Actions</h3>';

if ($maturityLevel === 'Foundation') {
    $html .= '<div class="improvement-section">
        <h4>Priority Actions for Foundation Level</h4>
        <p>Your organization is in the early stages of digital sovereignty. Focus on building foundational capabilities:</p>
        <ul>
            <li><strong>Assess Current State:</strong> Conduct detailed inventory of data locations, vendor dependencies, and compliance requirements</li>
            <li><strong>Define Strategy:</strong> Develop a digital sovereignty roadmap aligned with your business objectives and regulatory obligations</li>
            <li><strong>Establish Governance:</strong> Create executive sponsorship and steering committee for sovereignty initiatives</li>
            <li><strong>Address Quick Wins:</strong> Implement encryption key management (BYOK/HYOK) and data residency controls</li>
            <li><strong>Build Expertise:</strong> Train technical teams on sovereign technologies and compliance frameworks</li>
            <li><strong>Evaluate Solutions:</strong> Research open-source and sovereign-ready platforms that reduce vendor lock-in</li>
        </ul>
        <h4>Recommended Focus Areas:</h4>
        <ul>
            <li>Data sovereignty and encryption controls</li>
            <li>Open-source adoption strategy</li>
            <li>Compliance framework alignment (GDPR, NIS2, etc.)</li>
            <li>Vendor risk assessment and diversification</li>
        </ul>
    </div>';
} elseif ($maturityLevel === 'Developing') {
    $html .= '<div class="improvement-section">
        <h4>Advancement Actions for Developing Level</h4>
        <p>Your organization is making progress building digital sovereignty capabilities. Continue your momentum:</p>
        <ul>
            <li><strong>Strengthen Foundations:</strong> Solidify controls in domains where you scored lowest (0-1 points)</li>
            <li><strong>Implement Standards:</strong> Adopt open standards and containerization to improve portability</li>
            <li><strong>Enhance Data Controls:</strong> Ensure all sensitive data has proper residency and encryption controls</li>
            <li><strong>Build Resilience:</strong> Develop disaster recovery and business continuity plans for geopolitical scenarios</li>
            <li><strong>Expand Expertise:</strong> Grow in-house technical capabilities for managing sovereign infrastructure</li>
            <li><strong>Document Policies:</strong> Create formal policies for open-source adoption and vendor selection</li>
        </ul>
        <h4>Recommended Focus Areas:</h4>
        <ul>
            <li>Cloud platform portability and migration testing</li>
            <li>Security log sovereignty and audit controls</li>
            <li>Operational independence from external providers</li>
            <li>Executive alignment and budget allocation</li>
        </ul>
    </div>';
} elseif ($maturityLevel === 'Strategic') {
    $html .= '<div class="improvement-section">
        <h4>Growth Actions for Strategic Level</h4>
        <p>Your organization has established strong capabilities. Continue building momentum:</p>
        <ul>
            <li><strong>Close Remaining Gaps:</strong> Address specific weaknesses identified in lower-scoring domains</li>
            <li><strong>Enhance Portability:</strong> Migrate workloads to open standards and test cloud portability</li>
            <li><strong>Strengthen Controls:</strong> Implement advanced monitoring, audit rights, and security log sovereignty</li>
            <li><strong>Expand Open Source:</strong> Increase use of open-source software and participate in strategic projects</li>
            <li><strong>Test Resilience:</strong> Validate disaster recovery plans and operational independence from cloud providers</li>
            <li><strong>Pursue Certifications:</strong> Obtain national security certifications (NIS2, SecNumCloud, etc.)</li>
        </ul>
        <h4>Recommended Resources:</h4>
        <ul>
            <li>Digital Sovereignty best practices and frameworks</li>
            <li>Cloud migration and portability guides</li>
            <li>National certification requirements documentation</li>
            <li>Open-source governance policies</li>
        </ul>
    </div>';
} else {
    $html .= '<div class="improvement-section">
        <h4>Optimization Actions for Advanced Level</h4>
        <p>Your organization demonstrates strong sovereignty capabilities. Maintain and enhance your position:</p>
        <ul>
            <li><strong>Maintain Excellence:</strong> Continuously monitor and update sovereignty controls as regulations evolve</li>
            <li><strong>Share Knowledge:</strong> Document and share best practices internally and with industry peers</li>
            <li><strong>Lead Innovation:</strong> Contribute to open-source projects and influence sovereignty standards</li>
            <li><strong>Expand Scope:</strong> Apply sovereignty principles to emerging technologies (AI, edge computing, IoT)</li>
            <li><strong>Regular Validation:</strong> Conduct periodic audits and re-certifications to maintain compliance</li>
            <li><strong>Stay Informed:</strong> Monitor geopolitical changes and emerging regulations that may impact your strategy</li>
        </ul>
        <p><strong>Note:</strong> Digital sovereignty is a continuous journey. Regulations and threats evolve, requiring ongoing attention and investment to maintain your advanced posture.</p>
    </div>';
}

$html .= '</div>';

// Questions to Research section
if (!empty($unknownQuestions)) {
    $html .= '<div class="section">
        <h3>Questions to Research</h3>
        <p>The following questions were marked as "Don\'t Know". Research these areas to get a complete picture of your organization\'s Digital Sovereignty readiness:</p>
        <div class="unknown-list">';

    $unknownByDomain = [];
    foreach ($unknownQuestions as $uq) {
        $unknownByDomain[$uq['domain']][] = $uq;
    }

    foreach ($unknownByDomain as $domainName => $domainUnknowns) {
        $html .= '<h4 style="color: #0066cc; margin-top: 15px;">' . htmlspecialchars($domainName) . '</h4>';
        foreach ($domainUnknowns as $uq) {
            $html .= '<div class="unknown-item">
                        <strong>' . htmlspecialchars($uq['question']) . '</strong>';
            if (!empty($uq['tooltip'])) {
                $html .= '<p style="margin: 5px 0 0 0; font-size: 10pt; color: #666;">' . htmlspecialchars($uq['tooltip']) . '</p>';
            }
            $html .= '</div>';
        }
    }

    $html .= '</div></div>';
}

$html .= '
    <div class="footer">
        <p>Generated by Viewfinder Lite - Digital Sovereignty Readiness Assessment</p>
        <p>' . htmlspecialchars($assessmentDate) . '</p>
    </div>
</body>
</html>';

// Configure Dompdf
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', false);
$options->set('defaultFont', 'Arial');

// Initialize Dompdf
$dompdf = new Dompdf($options);

// Load HTML content
$dompdf->loadHtml($html);

// Set paper size
$dompdf->setPaper('A4', 'portrait');

// Render PDF
$dompdf->render();

// Output PDF for download
$filename = 'DS-Readiness-Assessment-' . date('Y-m-d-His') . '.pdf';
$dompdf->stream($filename, ['Attachment' => true]);
