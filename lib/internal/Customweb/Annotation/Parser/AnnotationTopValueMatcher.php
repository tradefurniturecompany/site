<?php



class Customweb_Annotation_Parser_AnnotationTopValueMatcher extends Customweb_Annotation_Parser_AnnotationValueMatcher {

	protected function process($value){
		return array(
			'value' => $value 
		);
	}
}