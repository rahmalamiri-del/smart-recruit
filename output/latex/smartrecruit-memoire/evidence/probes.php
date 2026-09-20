<?php
// Sondes du memoire : aucun bootstrap Laravel, aucune lecture .env ou SQL.
declare(strict_types=1);
$root = dirname(__DIR__, 4);
require $root.'/vendor/autoload.php';
if (! function_exists('env')) {
    function env($key, $default = null) { return $default; }
}
// Le helper env de Laravel peut exister apres autoload ; aucune variable secrete
// n'est lue, seules les listes de competences sont utilisees ci-dessous.
$settings = require $root.'/config/smart_recruit.php';
$matcher = new SmartRecruit\Support\SemanticMatcher(
    $settings['skill_aliases'], $settings['skill_taxonomy']
);
$cases = [
    ['id'=>'angular_cloud_nosql','offer_text'=>'angular aws mongodb','cv_text'=>'angular aws mongodb','required'=>[],'candidate'=>[],'experience'=>0],
    ['id'=>'alias_explicit','offer_text'=>'Laravel','cv_text'=>'PHP','required'=>['Laravel'],'candidate'=>['PHP'],'experience'=>0],
    ['id'=>'worked_example','offer_text'=>'java react','cv_text'=>'java sql','required'=>[],'candidate'=>[],'experience'=>2],
    ['id'=>'identical_java','offer_text'=>'java','cv_text'=>'java','required'=>[],'candidate'=>[],'experience'=>0],
    ['id'=>'unrelated','offer_text'=>'java react','cv_text'=>'python sql','required'=>[],'candidate'=>[],'experience'=>0],
    ['id'=>'empty','offer_text'=>'','cv_text'=>'','required'=>[],'candidate'=>[],'experience'=>0],
];
$out = ['php_version'=>PHP_VERSION,'cases'=>[]];
foreach ($cases as $case) {
    $offer = ['title'=>'','description'=>$case['offer_text'],'required_skills'=>$case['required']];
    $student = ['headline'=>'','education'=>'','cv_text'=>$case['cv_text'],'skills'=>$case['candidate'],'experience_years'=>$case['experience']];
    $out['cases'][] = $case + ['php'=>$matcher->score($offer,$student)];
}
$parser = new SmartRecruit\Support\CvParser($matcher);
$out['age_probe'] = $parser->parse(null,'Age : 24 ans. Licence en informatique.');
$out['punctuation_probe'] = $matcher->extractSkills('Python.');
$method = new ReflectionMethod($matcher,'tokens');
$out['token_probe'] = $method->invoke($matcher,'React.js');
echo json_encode($out, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES), PHP_EOL;
