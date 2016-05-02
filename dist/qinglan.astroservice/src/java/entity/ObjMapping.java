/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package entity;

import java.io.Serializable;
import javax.persistence.*;
import javax.validation.constraints.NotNull;
import javax.xml.bind.annotation.XmlRootElement;

/**
 *
 * @author roxy
 */
@Entity
@Table(name = "obj_mapping")
@XmlRootElement
@NamedQueries({
    @NamedQuery(name = "ObjMapping.findAll", query = "SELECT o FROM ObjMapping o"),
    @NamedQuery(name = "ObjMapping.findByObjMappingId", query = "SELECT o FROM ObjMapping o WHERE o.objMappingId = :objMappingId")})
public class ObjMapping implements Serializable {
    private static final long serialVersionUID = 1L;
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Basic(optional = false)
    @NotNull
    @Column(name = "obj_mapping_id")
    private Long objMappingId;
    @JoinColumn(name = "obj_src_id", referencedColumnName = "object_id")
    @ManyToOne(optional = false)
    private ObjectInfo objSrcId;
    @JoinColumn(name = "obj_tar_id", referencedColumnName = "object_id")
    @ManyToOne(optional = false)
    private ObjectInfo objTarId;

    public ObjMapping() {
    }

    public ObjMapping(Long objMappingId) {
        this.objMappingId = objMappingId;
    }

    public Long getObjMappingId() {
        return objMappingId;
    }

    public void setObjMappingId(Long objMappingId) {
        this.objMappingId = objMappingId;
    }

    public ObjectInfo getObjSrcId() {
        return objSrcId;
    }

    public void setObjSrcId(ObjectInfo objSrcId) {
        this.objSrcId = objSrcId;
    }

    public ObjectInfo getObjTarId() {
        return objTarId;
    }

    public void setObjTarId(ObjectInfo objTarId) {
        this.objTarId = objTarId;
    }

    @Override
    public int hashCode() {
        int hash = 0;
        hash += (objMappingId != null ? objMappingId.hashCode() : 0);
        return hash;
    }

    @Override
    public boolean equals(Object object) {
        // TODO: Warning - this method won't work in the case the id fields are not set
        if (!(object instanceof ObjMapping)) {
            return false;
        }
        ObjMapping other = (ObjMapping) object;
        if ((this.objMappingId == null && other.objMappingId != null) || (this.objMappingId != null && !this.objMappingId.equals(other.objMappingId))) {
            return false;
        }
        return true;
    }

    @Override
    public String toString() {
        return "entity.ObjMapping[ objMappingId=" + objMappingId + " ]";
    }
    
}
