<?php



class Customweb_Annotation_Parser_AnnotationHashMatcher extends Customweb_Annotation_Parser_ParallelMatcher {

	protected function build(){
		$this->add(new Customweb_Annotation_Parser_AnnotationPairMatcher());
		$this->add(new Customweb_Annotation_Parser_AnnotationMorePairsMatcher());
	}
}