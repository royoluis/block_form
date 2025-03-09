<?php

declare(strict_types=1);

namespace Drupal\block_form\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a Block form.
 */
final class RegistrationForm extends FormBase {
  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   *   Provides access to entity storage and definitions.
   */
  protected $entityTypeManager;

  /**
   * Class constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager service used to interact with entity types.
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager) {
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * Factory method to create an instance of this class.
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The service container from which dependencies are retrieved.
   *
   * @return static
   *   A new instance of this class with the required dependencies injected.
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'block_form_registration';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#required' => TRUE,
    ];

    $form['surname'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Surname'),
      '#required' => TRUE,
    ];

    $form['phone_number'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Phone Number'),
      '#required' => TRUE,
      '#attributes' => [
        'pattern' => '[0-9]+',
        'title' => $this->t('Only numbers are allowed'),
      ],
    ];

    $form['email'] = [
      '#type' => 'email',
      '#title' => $this->t('Email'),
      '#required' => TRUE,
    ];
  
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    // @todo Validate the form here.
    // Example:
    // @code
    //   if (mb_strlen($form_state->getValue('message')) < 10) {
    //     $form_state->setErrorByName(
    //       'message',
    //       $this->t('Message should be at least 10 characters.'),
    //     );
    //   }
    // @endcode
  }

  /**
   * {@inheritdoc}
   */
  public function entityCreate($name, $surname, $phone_number, $email) {
    $values = [
      'title' => 'Reg: ' . $name . $surname,
      'type' => 'registration',
      'field_name' => $name,
      'field_surname' => $surname,
      'field_phone_number' => $phone_number,
      'field_email' => $email
    ];
    $node = $this->entityTypeManager->getStorage('node')->create($values);
    $node->save();
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $name = $form_state->getValue('name');
    $surname = $form_state->getValue('surname');
    $phone_number = $form_state->getValue('phone_number');
    $email = $form_state->getValue('email');

    $this->entityCreate($name, $surname, $phone_number, $email);
    $this->messenger()->addStatus($this->t('Your registration has been sent. Thank you'));
    $form_state->setRedirect('<front>');
  }

}
