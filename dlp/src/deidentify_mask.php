<?php

/**
 * Copyright 2016 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
namespace Google\Cloud\Samples\Dlp;

# [START deidentify_mask]
use Google\Cloud\Dlp\V2\CharacterMaskConfig;
use Google\Cloud\Dlp\V2\DlpServiceClient;
use Google\Cloud\Dlp\V2\InfoType;
use Google\Cloud\Dlp\V2\PrimitiveTransformation;
use Google\Cloud\Dlp\V2\DeidentifyConfig;
use Google\Cloud\Dlp\V2\InspectConfig;
use Google\Cloud\Dlp\V2\InfoTypeTransformations_InfoTypeTransformation;
use Google\Cloud\Dlp\V2\InfoTypeTransformations;
use Google\Cloud\Dlp\V2\ContentItem;

/**
 * Deidentify sensitive data in a string by masking it with a character.
 * @param string $callingProject The GCP Project ID to run the API call under
 * @param string $string The string to deidentify
 * @param int $numberToMask (Optional) The maximum number of sensitive characters to mask in a match
 * @param string $maskingCharacter (Optional) The character to mask matching sensitive data with
 */
function deidentify_mask(
    $callingProjectId,
    $string,
    $numberToMask = 0,
    $maskingCharacter = 'x')
{
    // Instantiate a client.
    $dlp = new DlpServiceClient();

    // The infoTypes of information to mask
    $ssnInfoType = new InfoType();
    $ssnInfoType->setName('US_SOCIAL_SECURITY_NUMBER');
    $infoTypes = [$ssnInfoType];

    // Create the masking configuration object
    $maskConfig = new CharacterMaskConfig();
    $maskConfig->setMaskingCharacter($maskingCharacter);
    $maskConfig->setNumberToMask($numberToMask);

    // Create the information transform configuration objects
    $primitiveTransformation = new PrimitiveTransformation();
    $primitiveTransformation->setCharacterMaskConfig($maskConfig);

    $infoTypeTransformation = new InfoTypeTransformations_InfoTypeTransformation();
    $infoTypeTransformation->setPrimitiveTransformation($primitiveTransformation);

    $infoTypeTransformations = new InfoTypeTransformations();
    $infoTypeTransformations->setTransformations([$infoTypeTransformation]);

    // Create the deidentification configuration object
    $deidentifyConfig = new DeidentifyConfig();
    $deidentifyConfig->setInfoTypeTransformations($infoTypeTransformations);

    $item = new ContentItem();
    $item->setValue($string);

    $parent = $dlp->projectName($callingProjectId);

    // Run request
    $response = $dlp->deidentifyContent($parent, Array(
        'deidentifyConfig' => $deidentifyConfig,
        'item' => $item
    ));

    $likelihoods = ['Unknown', 'Very unlikely', 'Unlikely', 'Possible',
                    'Likely', 'Very likely'];

    // Print the results
    $deidentifiedValue = $response->getItem()->getValue();
    print_r($deidentifiedValue);
}
# [END deidentify_mask]
