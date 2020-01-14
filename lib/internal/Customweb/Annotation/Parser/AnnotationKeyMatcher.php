<?php



class Customweb_Annotation_Parser_AnnotationKeyMatcher extends Customweb_Annotation_Parser_ParallelMatcher {

	protected function build(){
		$this->add(new Customweb_Annotation_Parser_RegexMatcher('[a-zA-Z][a-zA-Z0-9_]*'));
		$this->add(new Customweb_Annotation_Parser_AnnotationStringMatcher());
		$this->add(new Customweb_Annotation_Parser_AnnotationIntegerMatcher());
	}
}