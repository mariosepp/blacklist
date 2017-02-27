<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type as T;

class NameCheckType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('name', T\TextType::class, array(
					'required' => true
			))
			->add('blacklist_file', T\FileType::class, array(
					'required' => false
			))
			->add('noise_file', T\FileType::class, array(
					'required' => false
			))
			->add('submit', T\SubmitType::class)
		;
	}
}