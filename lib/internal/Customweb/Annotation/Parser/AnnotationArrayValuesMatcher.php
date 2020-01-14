<?php



class Customweb_Annotation_Parser_AnnotationArrayValuesMatcher extends Customweb_Annotation_Parser_ParallelMatcher {

	protected function build(){
		$this->add(new Customweb_Annotation_Parser_AnnotationArrayValueMatcher());
		$this->add(new Customweb_Annotation_Parser_AnnotationMoreValuesMatcher());
	}
}
