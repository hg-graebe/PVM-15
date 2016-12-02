<?php

class pvmkit_object_text extends pvmkit_object {
	
	protected $type = 'text';
	
	/*
	 *	html output of object
	 */
	public function __toString(){
		return '<div><p>[' . $this->get_type() . '] Object-ID: ' . $this->object_id . ' | Author-ID: ' . $this->author->get_author_id() . '</p><p>' . $this->content . '</p></div>';
	}
}

?>