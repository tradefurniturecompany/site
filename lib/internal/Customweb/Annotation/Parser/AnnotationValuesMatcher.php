<?php



class Customweb_Annotation_Parser_AnnotationValuesMatcher extends Customweb_Annotation_Parser_ParallelMatcher {

	protected function build(){
		$this->add(new Customweb_Annotation_Parser_AnnotationTopValueMatcher());
		$this->add(new Customweb_Annotation_Parser_AnnotationHashMatcher());
	}
}