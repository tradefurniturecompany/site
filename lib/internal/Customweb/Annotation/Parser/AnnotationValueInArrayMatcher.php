<?php



class Customweb_Annotation_Parser_AnnotationValueInArrayMatcher extends Customweb_Annotation_Parser_AnnotationValueMatcher {

	public function process($value){
		return array(
			$value 
		);
	}
}