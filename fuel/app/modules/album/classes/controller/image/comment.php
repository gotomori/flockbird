<?php
namespace Album;

class Controller_Image_comment extends \Controller_Site
{
	protected $check_not_auth_action = array(
		'list',
	);

	public function before()
	{
		parent::before();
	}

	public function action_create($album_image_id = null)
	{
		if (!$album_image_id || !$album_image = Model_AlbumImage::find($album_image_id))
		{
			throw new \HttpNotFoundException;
		}

		// Lazy validation
		if (\Input::post('body'))
		{
			\Util_security::check_csrf();

			// Create a new comment
			$comment = new Model_AlbumImageComment(array(
				'body' => \Input::post('body'),
				'album_image_id' => $album_image_id,
				'member_id' => $this->u->id,
			));

			// Save the post and the comment will save too
			if ($comment->save())
			{
				\Session::set_flash('message', __('message_comment_complete'));
			}
			else
			{
				\Session::set_flash('error', __('message_comment_failed'));
			}

			\Response::redirect('album/image/'.$album_image_id);
		}
		else
		{
			Controller_Image::action_detail($album_image_id);
		}
	}

	/**
	 * Album image comment delete
	 * 
	 * @access  public
	 * @params  integer
	 * @return  Response
	 */
	public function action_delete($id = null)
	{
		$id = (int)$id;
		$album_image_comment = Model_AlbumImageComment::check_authority($id, $this->u->id);
		\Util_security::check_csrf(\Input::get(\Config::get('security.csrf_token_key')));

		$album_image_id = $album_image_comment->album_image_id;
		$album_image_comment->delete();

		\Session::set_flash('message', __('message_delete_complete_for', array('label' => t('form.comment'))));
		\Response::redirect('album/image/'.$album_image_id);
	}
}
