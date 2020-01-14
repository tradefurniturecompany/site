<?php



class Customweb_Annotation_Parser_AnnotationArrayValueMatcher extends Customweb_Annotation_Parser_ParallelMatcher {

	protected function build(){
		$this->add(new Customweb_Annotation_Parser_AnnotationValueInArrayMatcher());
		$this->add(new Customweb_Annotation_Parser_AnnotationPairMatcher());
	}
}
