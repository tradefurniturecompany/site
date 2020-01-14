<?php



class Customweb_Annotation_Parser_AnnotationArrayMatcher extends Customweb_Annotation_Parser_ParallelMatcher {

	protected function build(){
		$this->add(new Customweb_Annotation_Parser_ConstantMatcher('{}', array()));
		$values_matcher = new Customweb_Annotation_Parser_SimpleSerialMatcher(1);
		$values_matcher->add(new Customweb_Annotation_Parser_RegexMatcher('\s*{\s*'));
		$values_matcher->add(new Customweb_Annotation_Parser_AnnotationArrayValuesMatcher());
		$values_matcher->add(new Customweb_Annotation_Parser_RegexMatcher('\s*}\s*'));
		$this->add($values_matcher);
	}
}
