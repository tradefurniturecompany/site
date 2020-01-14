<?php



class Customweb_Annotation_Parser_NestedAnnotationMatcher extends Customweb_Annotation_Parser_AnnotationMatcher {

	protected function process($result){
		return new Customweb_Annotation_Cache_Annotation($result[1], $result[2]);

	}
}