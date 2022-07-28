<?php

namespace EventEspressoBatchRequest\JobHandlers;

use EE_Error;
use EE_Registry;
use EEM_Base;
use EventEspresso\core\exceptions\UnexpectedEntityException;
use EventEspresso\core\services\orm\tree_traversal\ModelObjNode;
use EventEspresso\core\services\orm\tree_traversal\NodeGroupDao;
use EventEspressoBatchRequest\Helpers\JobParameters;
use EventEspressoBatchRequest\Helpers\JobStepResponse;
use EventEspressoBatchRequest\JobHandlerBaseClasses\JobHandler;
use ReflectionException;

/**
 * Class EventDeletion
 *
 * Given a job code (eg generated by PreviewEventDeletion), performs the deletion of the indicated items.
 *
 * @package        Event Espresso
 * @author         Mike Nelson
 * @since          4.10.12.p
 *
 */
class ExecuteBatchDeletion extends JobHandler
{
    /**
     * @var NodeGroupDao
     */
    protected $model_obj_node_group_persister;


    public function __construct(NodeGroupDao $model_obj_node_group_persister)
    {
        $this->model_obj_node_group_persister = $model_obj_node_group_persister;
    }


    // phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps


    /**
     *
     * @param JobParameters $job_parameters
     * @return JobStepResponse
     * @throws EE_Error
     * @throws ReflectionException
     */
    public function create_job(JobParameters $job_parameters)
    {
        $deletion_job_code = $job_parameters->request_datum('deletion_job_code', null);
        $roots             = $this->model_obj_node_group_persister->getModelObjNodesInGroup($deletion_job_code);
        if ($roots === null) {
            throw new UnexpectedEntityException(
                $roots,
                'array',
                esc_html__(
                    'The job seems to be stale. Please press the back button in your browser twice.',
                    'event_espresso'
                )
            );
        }
        $models_and_ids_to_delete = [];
        foreach ($roots as $root) {
            if (! $root instanceof ModelObjNode) {
                throw new UnexpectedEntityException($root, 'ModelObjNode');
            }
            $models_and_ids_to_delete = array_replace_recursive($models_and_ids_to_delete, $root->getIds());
        }
        $job_parameters->set_extra_data(
            [
                'models_and_ids_to_delete' => $models_and_ids_to_delete,
            ]
        );
        // Find the job's actual size.
        $job_size = 0;
        foreach ($models_and_ids_to_delete as $ids) {
            $job_size += count($ids);
        }
        $job_parameters->set_job_size($job_size);
        return new JobStepResponse(
            $job_parameters,
            esc_html__('Beginning to delete items...', 'event_espresso')
        );
    }


    /**
     * Performs another step of the job
     *
     * @param JobParameters $job_parameters
     * @param int           $batch_size
     * @return JobStepResponse
     * @throws EE_Error
     * @throws ReflectionException
     */
    public function continue_job(JobParameters $job_parameters, $batch_size = 50)
    {
        // We already have the items IDs. So deleting is really fast. Let's speed it up.
        $batch_size               *= 10;
        $units_processed          = 0;
        $models_and_ids_to_delete = $job_parameters->extra_datum('models_and_ids_to_delete', []);
        // Build a new list of everything leftover after this request's of deletions.
        $models_and_ids_remaining = [];
        foreach ($models_and_ids_to_delete as $model_name => $ids_to_delete) {
            // don't delete logs
            if ($model_name === 'Change_Log') {
                continue;
            }
            if ($units_processed < $batch_size) {
                /** @var EEM_Base $model */
                $model                    = EE_Registry::instance()->load_model($model_name);
                $ids_to_delete_this_query = array_slice($ids_to_delete, 0, $batch_size - $units_processed, true);
                if ($model->has_primary_key_field()) {
                    $where_conditions = [
                        $model->primary_key_name() => [
                            'IN',
                            $ids_to_delete_this_query,
                        ],
                    ];
                } else {
                    $where_conditions = [
                        'OR' => [],
                    ];
                    foreach ($ids_to_delete_this_query as $index_primary_key_string) {
                        $keys_n_values                                                =
                            $model->parse_index_primary_key_string($index_primary_key_string);
                        $where_conditions['OR'][ 'AND*' . $index_primary_key_string ] = $keys_n_values;
                    }
                }
                // Deleting time!
                // The model's deletion method reports every ROW deleted, and in the case of CPT models that will be
                // two rows deleted for event CPT item. So don't rely on it for the count of items deleted.
                $model->delete_permanently([ $where_conditions ], false);
                $units_processed += count($ids_to_delete_this_query);
                $remaining_ids   = array_diff_key($ids_to_delete, $ids_to_delete_this_query);
                // If there's any more from this model, we'll do them next time.
                if (count($remaining_ids) > 0) {
                    $models_and_ids_remaining[ $model_name ] = $remaining_ids;
                }
            } else {
                $models_and_ids_remaining[ $model_name ] = $ids_to_delete;
            }
        }
        $job_parameters->mark_processed($units_processed);
        // All done deleting for this request. Is there anything to do next time?
        if (empty($models_and_ids_remaining)) {
            $job_parameters->set_status(JobParameters::status_complete);
            return new JobStepResponse(
                $job_parameters,
                esc_html__('Deletion complete.', 'event_espresso')
            );
        }
        $job_parameters->add_extra_data('models_and_ids_to_delete', $models_and_ids_remaining);
        return new JobStepResponse(
            $job_parameters,
            sprintf(
                esc_html__('Deleted %d items.', 'event_espresso'),
                $units_processed
            )
        );
    }


    /**
     * Performs any clean-up logic when we know the job is completed
     *
     * @param JobParameters $job_parameters
     * @return JobStepResponse
     */
    public function cleanup_job(JobParameters $job_parameters)
    {
        $this->model_obj_node_group_persister->deleteModelObjNodesInGroup(
            $job_parameters->request_datum('deletion_job_code')
        );
        // For backwards compatibility with how we used to delete events, make sure we still trigger the old action.
        $models_and_ids_to_delete = $job_parameters->extra_datum('models_and_ids_to_delete', []);
        foreach ($models_and_ids_to_delete['Event'] as $event_id) {
            // TrashLogger hooks into the following to create a log entry
            // so we know when and who permanently deleted this event.
            do_action(
                'AHEE__Events_Admin_Page___permanently_delete_event__after_event_deleted',
                $event_id,
                'Event',
                $job_parameters
            );
        }
        return new JobStepResponse(
            $job_parameters,
            esc_html__('All done', 'event_espresso')
        );
    }
}
// End of file EventDeletion.php
// Location: EventEspressoBatchRequest\JobHandlers/EventDeletion.php
