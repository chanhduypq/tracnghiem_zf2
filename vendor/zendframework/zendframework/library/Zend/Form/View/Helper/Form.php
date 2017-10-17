<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Form\View\Helper;

use Zend\Form\FieldsetInterface;
use Zend\Form\FormInterface;

/**
 * View helper for rendering Form objects
 */
class Form extends AbstractHelper
{
    /**
     * Attributes valid for this tag (form)
     *
     * @var array
     */
    protected $validTagAttributes = array(
        'accept-charset' => true,
        'action'         => true,
        'autocomplete'   => true,
        'enctype'        => true,
        'method'         => true,
        'name'           => true,
        'novalidate'     => true,
        'target'         => true,
    );

    /**
     * Invoke as function
     *
     * @param  null|FormInterface $form
     * @return Form
     */
    public function __invoke(FormInterface $form = null)
    {
        if (!$form) {
            return $this;
        }

        return $this->render($form);
    }

    /**
     * Render a form from the provided $form,
     *
     * @param  FormInterface $form
     * @return string
     */
    public function render(FormInterface $form)
    {
        if (method_exists($form, 'prepare')) {
            $form->prepare();
        }

        $formContent = '';

        foreach ($form as $element) {
            if ($element instanceof FieldsetInterface) {
                $formContent.= $this->getView()->formCollection($element);
            } else {
                $formContent.= $this->getView()->formRow($element);
            }
        }

        return $this->openTag($form) . $formContent . $this->closeTag();
    }

    /**
     * Generate an opening form tag
     *
     * @param  null|FormInterface $form
     * @return string
     */
    public function openTag(FormInterface $form = null)
    {
        $attributes = array(
            'action' => '',
            'method' => 'get',
        );

        if ($form instanceof FormInterface) {
            $formAttributes = $form->getAttributes();
            if (!array_key_exists('id', $formAttributes) && array_key_exists('name', $formAttributes)) {
                $formAttributes['id'] = $formAttributes['name'];
            }
            $attributes = array_merge($attributes, $formAttributes);
        }

        $tag = sprintf('<form %s>', $this->createAttributesString($attributes));

        return $tag;
    }

    /**
     * Generate a closing form tag
     *
     * @return string
     */
    public function closeTag()
    {
        return '</form>';
    }
    
    /**
         * function common
	 * @author Trần Công Tuệ <chanhduypq@gmail.com>
	 * gernerate thành các tag html của các input trong một form
	 * @param Core_Form $form
	 * @param integer $numberOfElement_per_row
	 * @param string $type
	 * @param boolean $echo_form_tag
	 * @param Zend_View_Interface $view
	 * @return string $html
	 */
	public function form($view,$form,$numberOfElement_per_row,$echo_form_tag,$type='table'){
		$elements=$form->getElements();
		if(count($elements)==0&&count($form->getSubForms())==0){
			return ;
		}				
		/*
		 * gernerate các thuộc tính, dòng lệnh jquery để các input xuất hiện theo chuẩn jquery
		*/
		$this->buildJqueryForElement($elements,$view);
		/**
		 * 
		 */
		if($echo_form_tag==true){
			?>
				<form name="form" enctype="multipart/form-data" id="form" action="<?php echo $view->url();?>" method="post">
                                    
				<?php
                                
				}
				if($type=='table'){			
					/*
					 * 
					 */
                                    
				?>
					<table width="100%"> 
						<tbody>     
						<?php  	
						$j=0;
						foreach ($elements as $element){						
							if($j==0){
								echo '<tr>';
							}
							if(!($element instanceof Core_Form_Element_Submit)
							){
								$element->getDecorator('label')->setOption('tag', null);
							}											 
							
							if(($element instanceof Core_Form_Element_Hidden)){
								$j--;
							}					
							else if(($element instanceof Core_Form_Element_File)
									||($element instanceof Core_Form_Element_Text)
									||($element instanceof Core_Form_Element_Textarea)
									||($element instanceof Core_Form_Element_Select)
									||($element instanceof Core_Form_Element_Multiselect)
									||($element instanceof Core_Form_Element_MultiCheckbox)
									||($element instanceof Core_Form_Element_Date)
									||($element instanceof Core_Form_Element_Password)
									||($element instanceof Core_Form_Element_Radio)
									)
							{	
												
							?>                 
					                 <td align="right" nowrap="nowrap"><?php echo $element->renderLabel();?></td>
					                 <td nowrap="nowrap">
					                     <?php 
					                     echo $element->renderViewHelper();
							             echo $view->getTopErrorMessage($element);
							             ?>
					                 </td>
					         	                    
		                 <?php 	   	
		                             
		                 	 }
		                 	 elseif (($element instanceof Core_Form_Element_Checkbox)                 	 		
		                 	 		)
		                 	 {                 	 	
		                 	 ?>
		                 	        <td align="right" nowrap="nowrap"></td>
					                <td nowrap="nowrap">
					                     <?php 
					                     echo $element->renderViewHelper();
					                     echo $element->renderLabel();
							             echo $view->getTopErrorMessage($element); 
							             ?>
					                 </td> 
		                 	 
		                 <?php 	 
		                 	 }
		                 	 elseif (($element instanceof Core_Form_Element_Submit)
		                 	 		||($element instanceof Core_Form_Element_Button)
		                 	 )
		                 	 {
		                 	 	?>                 	                  	        
		                 	 			                <td nowrap="nowrap" colspan="2" align="center">
		                 	 			                     <?php 
		                 	 			                     echo $element->renderViewHelper();                 	 			                      
		                 	 					             ?>
		                 	 			                 </td> 
		                 	                  	 
		   	                  <?php 	 
		                  	 }
		                 	 $j++;
		                 	 if($j==$numberOfElement_per_row){
		                 	 	echo '</tr>';
		                 	 	$j=0;  
		                 	 }
		                 	 
						}
						
						foreach ($elements as $element){					
						    if(!($element instanceof Core_Form_Element_Submit)
							){
								$element->getDecorator('label')->setOption('tag', null);
							}	
							if(($element instanceof Core_Form_Element_Hidden)){
								echo $element->renderViewHelper();
							}
						}
						
							?>                       
						 	</tbody>
					</table>
				<?php 		 
				}
				if(count($form->getSubForms())>0){
					foreach ($form->getSubForms() as $sub_form){
						$this->form($view, $sub_form, $numberOfElement_per_row,false);
					}
				}
				if($echo_form_tag==true){
				?>
				</form>
				<?php
				}	
	}
	/**
         * function common
	 * @author Trần Công Tuệ <chanhduypq@gmail.com>
	 * gernerate các thuộc tính, dòng lệnh jquery để các input xuất hiện theo chuẩn jquery
	 * @param array $elements
	 * @param Zend_View_Interface $view
	 * @return void
	 */
	private function buildJqueryForElement($elements,$view){
		$header_script_for_multiselect_exist=false;
		$header_link_for_multiselect_exist=false;
		if(!is_array($elements)||count($elements)==0){
			return ;
		}
		if($view==null||(!$view instanceof Zend_View_Interface)){
			return ;
		}
		foreach ($elements as $element){
			if(($element instanceof Core_Form_Element_Date)){
				$format=$element->getValidator('Date')->getFormat();
				if(
					$format==null
					||is_string($format)==false
					||trim($format)==""
					||strpos($format,"d")===false
					||strpos($format,"D")===false
					||strpos($format,"m")===false
					||strpos($format,"M")===false
					||strpos($format,"y")===false
					||strpos($format,"Y")===false
				){
					$format="dd/mm/yy";
				}				
				$format = strtolower($format);
				if(strpos($format,"dd")===false){
					$format=str_replace("d","dd",$format);
				}
				if(strpos($format,"mm")===false){
					$format=str_replace("m","mm",$format);
				}
				if(substr_count($format,"y")!=2){
					if(substr_count($format,"y")==1){
						$format=str_replace("y","yy",$format);
					}
					else if(substr_count($format,"y")==4){
						$format=str_replace("yyyy","yy",$format);
					}
				}								
				if($element->getStyleShowDatepicker()!='show'&&$element->getStyleShowDatepicker()!='slideDown'&&$element->getStyleShowDatepicker()!='fadeIn'){					                
					?>
								    <script type="text/javascript" src="<?php echo $view->baseUrl(); ?>/jquery-ui-1.10.3/ui/jquery.ui.effect-<?php echo $element->getStyleShowDatepicker();?>.js"></script>
								<?php
									}
					
								?>
								
								<script type="text/javascript">						
									jQuery(function($) {							  
										$( "input#<?php echo $element->getName();?>" ).datepicker({
											//"dateFormat":"dd/mm/yy",
											"dateFormat":"<?php echo $format;?>",
											"option":$.datepicker.regional['vi'],
											"showAnim":"<?php echo $element->getStyleShowDatepicker();?>",
											showOn: "button",
											buttonImage: "<?php echo $view->baseUrl('/images/calendar/calendar.gif');?>",
											buttonImageOnly: true,
											buttonText: 'Click để chọn ngày',
											showWeek: true,																
											changeMonth: true,
											changeYear: true
											/*minDate: -2,
											maxDate: "+1M +10D"*/
											<?php								
											$max_date=$element->getMaxDateValue();
											$min_date=$element->getMinDateValue();
											if($max_date!=null){																		
												echo ',maxDate:"'.$max_date.'"';									
											} 
											if($min_date!=null){										
												echo ',minDate:"'.$min_date.'"';									
											}
											?>
										});
									});
									</script>
						<?php		       	
							}
							else if(($element instanceof Core_Form_Element_Multiselect)){
								if($header_link_for_multiselect_exist==false){
									echo $view->headLink();					
							?>
								    <link rel="stylesheet" type="text/css" href="<?php echo $view->baseUrl(); ?>/css/jquery.multiselect.css" media="all" />
								    <link rel="stylesheet" type="text/css" href="<?php echo $view->baseUrl(); ?>/css/jquery.multiselect.filter.css" media="all" />	
			                <?php 						
									$header_link_for_multiselect_exist=true;						
								}
								if($header_script_for_multiselect_exist==false){
									echo $view->headScript();?>
									<script type="text/javascript" src="<?php echo $view->baseUrl(); ?>/js/jquery.multiselect.js"></script>
								    <script type="text/javascript" src="<?php echo $view->baseUrl(); ?>/js/jquery.multiselect.filter.js"></script>
							<?php 	
							        $header_script_for_multiselect_exist=true;	
								}					   
							?>
							    
								
								<script type="text/javascript">						
									jQuery(function($) {							  
										jQuery("select#<?php echo $element->getName();?>").multiselect({
											checkAllText:'Chọn tất cả',
											uncheckAllText:'Hủy tất cả',
											noneSelectedText:'Chọn <?php echo $element->getLabel();?> bên dưới ',		
											selectedText:'# trong số # giá trị',								
											    hide: ["explode", 1000]	
									    })
									    .multiselectfilter();
									    								    
									});
								</script> 
						<?php 	
							}
							else if(($element instanceof Core_Form_Element_Textarea)){?>
							    <script type="text/javascript">						
									jQuery(function($) {							  
										$("textarea#<?php echo $element->getName();?>").TextAreaExpander();
									});
								</script> 
						<?php 
							}
						}
		}
	public function setView(\Zend\View\Renderer\RendererInterface $view)
	{
		$this->view = $view;
	}
}
