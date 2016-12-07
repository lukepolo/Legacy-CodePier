<?php

namespace App\Http\Controllers\Site;

use App\Models\Site\Site;
use App\Traits\SystemFiles;
use App\Http\Controllers\Controller;

class SiteFeatureController extends Controller
{
    use SystemFiles;

    /**
     * @param $siteId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEditableFrameworkFiles($siteId)
    {
        $editableFiles = collect();
        $site = Site::findOrFail($siteId);

        if (! empty($site->framework)) {
            $files = [];

            foreach ($this->getSystemsFiles() as $system) {
                foreach ($this->getVersionsFromSystem($system) as $version) {
                    foreach ($this->getLanguagesFromVersion($version) as $language) {
                        foreach ($this->getFrameworksFromLanguage($language) as $framework) {
                            $language = substr($language, strrpos($language, '/') + 1);
                            $reflectionClass = $this->buildReflection($framework);
                            $files[$language][$reflectionClass->getShortName()] = $this->buildFileArray($reflectionClass, $site->path.'/');
                        }
                    }
                }
            }

            $languageAndFramework = explode('.', $site->framework);

            if (isset($files[$languageAndFramework[0]][$languageAndFramework[1]])) {
                $editableFiles = $editableFiles->merge($files[$languageAndFramework[0]][$languageAndFramework[1]]);
            }
        }

        return response()->json($editableFiles);
    }

    /**
     * @param $siteId
     * @return array
     */
    public function getSuggestedFeatures($siteId)
    {
        $site = Site::findOrFail($siteId);
        $suggestedFeatures = [];

        foreach ($this->getSystemsFiles() as $system) {
            foreach ($this->getVersionsFromSystem($system) as $version) {
                foreach ($this->getLanguagesFromVersion($version) as $language) {
                    if (strtolower(basename($language)) == strtolower($site->type)) {
                        $reflectionClass = $this->buildReflection($language.'/'.basename($language).'.php');

                        $suggestedFeatures = $reflectionClass->getDefaultProperties()['suggestedFeatures'];

                        if (! empty($site->framework)) {
                            $reflectionClass = $this->buildReflection($language.'/Frameworks'.str_replace(basename($language).'.', '/', $site->framework).'.php');
                            $suggestedFeatures = array_merge($suggestedFeatures, $reflectionClass->getDefaultProperties()['suggestedFeatures']);
                        }

                        break;
                    }
                }
            }
        }

        return $suggestedFeatures;
    }
}
