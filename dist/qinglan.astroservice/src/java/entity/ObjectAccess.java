/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

package entity;

import java.io.Serializable;
import javax.persistence.*;
import javax.xml.bind.annotation.XmlRootElement;

@Entity
@Table(name = "object_access_control")
@XmlRootElement
public class ObjectAccess implements Serializable
{
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Basic(optional = false)
    @Column(name = "object_access_id")
    private int object_access_id;
    
    @Column(name = "object_id")
    private int object_id;
    
    @Column(name = "survey_id")
    private int survey_id;
    
    @Column(name = "create_access")
    private int create_access;
    
    @Column(name = "modify_access")
    private int modify_access;
    
    @Column(name = "delete_access")
    private int delete_access;
    
    @Column(name = "view_access")
    private int view_access;
    
    @Column(name = "operator_type")
    private String operator_type;
    
    @Column(name = "operator_id")
    private int operator_id;
    
    public ObjectAccess()
    {
        
    }
    
    public ObjectAccess(int object_access_id, int object_id, int survey_id, int create_access, int modify_access, 
            int delete_access, int view_access, String operator_type, int operator_id)
    {
        this.object_access_id = object_access_id;
        this.object_id = object_id;
        this.survey_id = survey_id;
        this.create_access = create_access;
        this.modify_access = modify_access;
        this.delete_access = delete_access;
        this.view_access = view_access;
        this.operator_type = operator_type;
        this.operator_id = operator_id;
    }
    
    public int getObjectAccessId()
    {
        return object_access_id;
    }
    
    public void setObjectAccessId(int id)
    {
        object_access_id = id;
    }
    
    public int getObjectId()
    {
        return object_id;
    }
    
    public void setObjectId(int id)
    {
        object_id = id;
    }
    
    public int getSurveyId()
    {
        return survey_id;
    }
    
    public void setSurveyId(int id)
    {
        survey_id = id;
    }
    
    public int getCreateAccess()
    {
        return create_access;
    }
    
    public void setCreateAccess(int access)
    {
        create_access = access;
    }
    
    public int getModifyAccess()
    {
        return modify_access;
    }
    
    public void setModifyAccess(int access)
    {
        modify_access = access;
    }
    
    public int getDeleteAccess()
    {
        return delete_access;
    }
    
    public void setDeleteAccess(int access)
    {
        delete_access = access;
    }
    
    public int getViewAccess()
    {
        return view_access;
    }
    
    public void setViewAccess(int access)
    {
        view_access = access;
    }
    
    public String getOperatorType()
    {
        return operator_type;
    }
    
    public void setOperatorType(String type)
    {
        operator_type = type;
    }
    
    public int getOperatorId()
    {
        return operator_id;
    }
    
    public void setOperatorId(int id)
    {
        operator_id = id;
    }
}
